<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


require APPPATH.'/libraries/REST_Controller.php';
require APPPATH.'/libraries/Db_api.php';

class Restdb extends Db_api {

	var $db_id;
	var $get_table;
	var $get_user;
	var $get_local;
	var $get_format;	


	/**
	 * Index Page for this controller.
	 *
	 */
	public function index_get($user = null, $db = null, $table = null, $local = null)
	{
				
		$this->db_id  		= (empty($db)) 	  ? $this->input->get('db', TRUE)	 : $db;
		$this->get_table 	= (empty($table)) ? $this->input->get('table', TRUE) : $table;		
		$this->get_user 	= (empty($user))  ? $this->input->get('user', TRUE)	 : $user;		
		$this->get_local	= (empty($local)) ? $this->input->get('local', TRUE) : $local;		
		$this->get_format 	= $this->_detect_output_format(); 
		
		$this->get_limit  	= (empty($limit)) ? $this->input->get('limit', TRUE) : $limit;			
		$this->get_limit 	= (empty($this->get_limit) && $this->config->item('default_page_size')) ? $this->config->item('default_page_size') :  $this->get_limit;
		$this->get_page  	= (empty($page)) ? $this->input->get('page', TRUE) : $page;			
		
		
		// This separates what's set with the URI vs the settings pulled from the database with prepare_db which distinguishes if we're showing docs or the actual api
		$showdocs = ($this->get_local == 'true') ? false : null;
																
		$get_db = $this->prepare_db();

		if( (($showdocs !== false) && ($this->get_local == 'true')) || empty($this->get_table) ) {
			return $this->show_docs($this->db_id, $get_db['config'], $get_db['db_settings']);
		}
		
		$this->register_db( $this->db_id, $get_db['config'] );		
		//$this->register_custom_sql( 'democracymap', config_item('sql_args') );		
		
		$query = array('db' => $this->db_id, 'table' => $this->get_table, 'limit' => $this->get_limit, 'page' => $this->get_page);		
		
		$query = $this->parse_query($query);
		$this->set_db( $query['db'] );
		$results = $this->query( $query );
		
		$this->response($results, 200);
	}
	
	


	private function prepare_db() {
		
		if ($this->get_local == 'true') {
			if(strpos($this->db_id, ".$this->get_format")) $this->db_id = substr($this->db_id, 0, strpos($this->db_id, ".$this->get_format"));								
		} else {
			if(!empty($this->get_table) && strpos($this->get_table, ".$this->get_format")) $this->get_table = substr($this->get_table, 0, strpos($this->get_table, ".$this->get_format"));								
		}

		$query = $this->get_database($this->get_user, $this->db_id);			

		if ($query->num_rows() > 0) {

		   	$db_settings = $query->first_row('array');

			if ($db_settings['local']) {
				
				$this->get_local = 'true';
				$this->get_table = $db_settings['db_name']; 
				$db_name = $this->config->item('sqlite_data_path') . $db_settings['name_hash'] . '.db';
				$db_name = (substr($db_name, 0, 1) == '/') ? substr($db_name, 1, strlen($db_name) -1) : $db_name;
				
			} else {
				$db_name = $db_settings['db_name'];
			}
							

			$table_blacklist  = (!empty($db_settings['table_blacklist']))  ? array_map('trim', explode(',', $db_settings['table_blacklist'])) : array();
			$column_blacklist = (!empty($db_settings['column_blacklist'])) ? array_map('trim',explode(',', $db_settings['column_blacklist'])) : array();							

			$config = array($this->db_id => array(
														'name' 				=> $db_name,
														'username' 			=> $db_settings['db_username'],
														'password' 			=> $db_settings['db_password'],
														'server' 			=> $db_settings['db_server'],
														'port' 				=> $db_settings['db_port'],
														'type' 				=> $db_settings['type'],
														'table_blacklist' 	=> $table_blacklist,
														'column_blacklist' 	=> $column_blacklist), 
														'ttl' 				=> null);			
		}		
		
		
		
		
		
		$prepare_db = array('db_id' 			=> $this->db_id, 
							'config' 			=> $config, 
							'db_settings' 		=> $db_settings);
						
		return $prepare_db;				
		
	}	
	
	
	
	public function dashboard_get($user = null) {
					
			$data = $this->get_user_dbs($user);	
			$this->load->view('user_view', $data);
		
		
	}
	
	private function get_user_dbs($user = null) {
		
		$this->load->helper('restdb'); // used for properize
		
		if (empty($user) && !$this->session->userdata('username')) {	
			redirect('login');
		}			
			
		if(empty($user) && $this->session->userdata('username')) {
			$user =	$this->session->userdata('username');	
		}


		// Prepare output data
		$data = array();			
		
		// Get user data
		$query = $this->get_user($user);			
								
		if ($query->num_rows() > 0) {
			$data['user'] = $query->first_row('array');
			
			$full_name = (!empty($data['user']['name_full'])) ? $data['user']['name_full'] : $data['user']['username'];
			
			$data['user']['name_full'] = properize($full_name);
		}			
		
		// Then check for database entries for that user			
		$query = $this->get_database($user);			
								
		if ($query->num_rows() > 0) {
			$data['connections'] = $query->result_array();
		}
				
		return $data;					
		
	}
	
	
	public function show_docs($db_id, $db_config, $db_settings) {

		$this->register_db( $db_id, $db_config );	
		$tables = $this->allowed_tables($db_id);
		
		//var_dump($db_settings); exit;
		
		$data = array('db' => $db_settings, 'tables' => $tables);
		
		$this->load->view('docs_view', $data);
		
	}	
	
	
	public function swagger_get($user = null, $db = null) {

		$this->db_id  		= (empty($db)) 	  ? $this->input->get('db', TRUE)	 : $db;
		$this->get_user 	= (empty($user))  ? $this->input->get('user', TRUE)	 : $user;

		// These lines might not be necessary, but including just to be safe
		$this->get_format 	= (empty($table)) ? $this->_detect_output_format() : $table;		 			
		
		// Start constructing the Swagger Model
		$this->load->model('swagger_model', 'swagger');
		$this->swagger->swaggerVersion = "1.1";
		$this->swagger->apiVersion = "0.1";

		$basePath = (strpos(current_url(), '/api-docs')) ? substr(current_url(), 0, strpos(current_url(), '/api-docs')) : current_url();
		
		if(empty($this->db_id)) {
			
			$user = $this->get_user_dbs($user);
			$resources = $user['connections'];
			

			$this->swagger->basePath = 'http://'.$_SERVER['SEVER_NAME'].$basePath; //substr($basePath, 0, strrpos($basePath, '/'));
			unset($this->swagger->resourcePath);
			
			$this->swagger->apis = array();
			$api = $this->swagger->api();			
			
			foreach($resources as $resource) {
				
				$api['path'] = '/api-docs.{format}/' . $resource['name_url'];
				$api['description'] = (!empty($resource['description'])) ? $resource['description'] : '';
				unset($api['operations']);

				$this->swagger->apis[] = $api;				
				
			}
			
		}
		else {

			if(strpos($this->db_id, ".$this->get_format")) {
				$this->db_id = substr($this->db_id, 0, strpos($this->db_id, ".$this->get_format"));								
			}



			$basePath = (strpos(current_url(), '/api-docs')) ? substr(current_url(), 0, strpos(current_url(), '/api-docs')) : current_url();
			$this->swagger->basePath = 'http://'.$_SERVER['SERVER_NAME'].$basePath; //substr($basePath, 0, strrpos($basePath, '/'));
			$this->swagger->resourcePath = substr(current_url(), strrpos(current_url(), '/'));			

			$get_db = $this->prepare_db();
			$db_settings = $get_db['db_settings'];			

			$this->swagger->apis = array();
			$api = $this->swagger->api();


			if ($db_settings['local']) {
				
				$this->get_table = $db_settings['name_url']; 
				$this->swagger->resourcePath = '/local';
				
				$this->register_db( $this->db_id, $get_db['config'] );						
				$this->swagger->apis[] = $this->swagger_api($db_settings['db_name'], $this->get_table);
				
			} else {

				$this->register_db( $this->db_id, $get_db['config'] );		
				$tables = $this->allowed_tables($this->db_id);

				foreach($tables as $table) {
							
					$this->swagger->apis[] = $this->swagger_api($table, $table);

				}
			}
			
		}
		
		
			
		
		$this->response($this->swagger, 200);
		
	}	
	
	
	private function get_database($user_url, $name_url = null) {
		
		$query = array('user_url' => $user_url);		

		if (!empty($name_url)) {
			$query['name_url'] = $name_url;		
		}
				
		return $this->db->get_where('db_connections', $query);				
		
	}
	
	
	private function get_user($user_url) {
		
		$query = array('username_url' => $user_url);		
				
		return $this->db->get_where('users_auth', $query);				
		
	}	
	
	public function add_get() {
		
		$this->load->view('add_view');
		
	}	
	
	
	public function add_post() {
		
		if (empty($user) && !$this->session->userdata('username')) {	
			redirect('login');
		}		
			
		$this->load->helper('restdb'); // used for slugify	
						
		$name_url = slugify($this->input->post('db_name', TRUE));
				
		
			$data = array(
						'db_name'           => 	$this->input->post('db_name', TRUE),
						'name_full'         => 	$this->input->post('name_full', TRUE),
						'name_url'          => 	$name_url,
						'name_hash'         => 	NULL,
						'description'       => 	$this->input->post('description', TRUE),
						'user_id'           => 	1,
						'user_url'          => 	$this->session->userdata('username'),
						'db_username'       => 	$this->input->post('db_username', TRUE),
						'db_password'       => 	$this->input->post('db_password', TRUE),
						'db_server'         => 	$this->input->post('db_server', TRUE),
						'db_port'           => 	$this->input->post('db_port', TRUE),
						'local'             => 	0,
						'type'              => 	$this->input->post('type', TRUE),
						'table_blacklist'   => 	$this->input->post('table_blacklist', TRUE),
						'column_blacklist'  => 	$this->input->post('column_blacklist', TRUE),
					);
		
		$this->db->insert('db_connections', $data);

        header('Location: '.base_url('dashboard'));die();
	}
	

	public function router_get($user_url = null, $name_url = null, $table_name = null) {								
				
		$this->index_get($user_url, $name_url, $table_name);		
						
	}
	
	public function router_local_get($user_url = null, $name_url = null) {								
		
		$table_name = $name_url;		
		$local = 'true';		
				
		$this->index_get($user_url, $name_url, $table_name, $local);		
					
	}
	
	private function swagger_api($table_name, $table_url) {
		
			$api['path'] = $this->swagger->resourcePath . '/' . $table_url . '.{format}';
			$api['description'] = '';

			$operations = $this->swagger->operations();			
			$operations['httpMethod'] = 'GET';	
			$operations['nickname'] = $table_url;			
			$operations['dataType'] = 'string';							
			$operations['required'] = false;

					
			// ###### Parameters ######
			
			// Column
			$p_column = $this->swagger->parameters();				
			$p_column['paramType'] 		= 'query';
			$p_column['name'] 			= 'column';				
			$p_column['description'] 	= 'A field (column name) in the table';								
			$p_column['required'] 		= false;																
			$p_column['dataType']	 	= 'string';											
			$p_column['allowMultiple'] 	= false;	
			
			$allowableValues 				= $this->swagger->allowableValues();
			unset($allowableValues['min']);
			unset($allowableValues['max']);
						
			$allowableValues['valueType'] 	= 'LIST';	
			$allowableValues['values']		= $this->get_columns($table_name, $this->db_id);															
			

			
			if ($allowableValues['values']) {
				$p_column['allowableValues']	= $allowableValues;
			}
			
			
			// Value
			$p_value = $p_column;
			unset($p_value['allowableValues']);
			
			$p_value['name'] 			= 'value';				
			$p_value['description'] 	= 'A value within the specified column';
			
			
			// Order
			$p_order = $p_column;
			$p_order['name'] 			= 'order_by';				
			$p_order['description'] 	= 'Name of column to sort by';								
			$p_order['required'] 		= false;																
			$p_order['dataType']	 	= 'string';											
			$p_order['allowMultiple'] 	= false;			
			
			// Direction
			$p_direction = $p_value;
			
			$p_direction['name'] 			= 'direction';				
			$p_direction['description'] 	= 'Direction to sort results';			
			
			$allowableValues 				= $this->swagger->allowableValues();
			$allowableValues['valueType'] 	= 'LIST';	
			$allowableValues['values']		= array('ASC', 'DESC');			

			$p_direction['allowableValues'] 	= $allowableValues;
			
			// Limit
			$p_limit = $p_column;
			$p_limit['name']			= 'limit';
			$p_limit['description']		= 'Maximum number of results to return';	
			$p_limit['dataType']	 	= 'int';	
						
			$allowableValues = $this->swagger->allowableValues();
			$allowableValues['max'] 	= 1000;
			$allowableValues['min'] 	= 1;														
			$allowableValues['valueType'] 	= 'RANGE';																		
			
			$p_limit['allowableValues'] = $allowableValues;							
				
			// Page								
			$p_page = $p_limit;
			$p_page['name']			= 'page';
			$p_page['description']	= 'The offset for pagination. Page size is defined by "limit"';	
			$p_page['dataType']	 	= 'int';
			unset($p_page['allowableValues']);

			$operations['parameters'] = array($p_column, $p_value, $p_order, $p_direction, $p_limit, $p_page);

			unset($operations['notes']);
			unset($operations['errorResponses']);	
		
			$api['operations'] = array($operations);
	

			return $api;		
		
	}
	
		
	
	
}
