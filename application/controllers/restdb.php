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
														
		$get_db = $this->prepare_db();

		if($this->get_local !== 'true' && empty($this->get_table)) {
			return $this->show_docs($this->db_id, $get_db['config'], $get_db['db_settings']);
		}
		
		$this->register_db( $this->db_id, $get_db['config'] );		
		//$this->register_custom_sql( 'democracymap', config_item('sql_args') );		
		
		$query = array('db' => $this->db_id, 'table' => $this->get_table);		
		
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
			

			$this->swagger->basePath = $basePath; //substr($basePath, 0, strrpos($basePath, '/'));			
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

			if(strpos($this->db_id, ".$this->get_format")) $this->db_id = substr($this->db_id, 0, strpos($this->db_id, ".$this->get_format"));								


			$get_db = $this->prepare_db();

			$db_settings = $get_db['db_settings'];

			if ($db_settings['local']) {
				$this->get_table = $db_settings['db_name']; 
			}

			$this->register_db( $this->db_id, $get_db['config'] );		

			$tables = $this->allowed_tables($this->db_id);

			$basePath = (strpos(current_url(), '/api-docs')) ? substr(current_url(), 0, strpos(current_url(), '/api-docs')) : current_url();

			$this->swagger->basePath = $basePath; //substr($basePath, 0, strrpos($basePath, '/'));			
			$this->swagger->resourcePath = substr(current_url(), strrpos(current_url(), '/'));			

			$this->swagger->apis = array();
			$api = $this->swagger->api();

			foreach($tables as $table) {

				$api['path'] = '/' . $table;
				$api['description'] = '';

				$operations = $this->swagger->operations();			
				$operations['httpMethod'] = 'GET';	
				$operations['nickname'] = $table;			
				$operations['responseClass'] = 'string';							
				$operations['summary'] = '';

				unset($operations['notes']);
				unset($operations['parameters']);
				unset($operations['errorResponses']);	
				
					
				
				$api['operations'] = array($operations);
			
				

				$this->swagger->apis[] = $api;

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
		
		
		redirect('/dashboard');
		
	}	
	

	public function router_get($user_url = null, $name_url = null, $table_name = null) {								
				
		$this->index_get($user_url, $name_url, $table_name);		
						
	}
	
	public function router_local_get($user_url = null, $name_url = null) {								
		
		$table_name = $name_url;		
		$local = 'true';		
				
		$this->index_get($user_url, $name_url, $table_name, $local);		
					
	}
	
		
	
	
}
