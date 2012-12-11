<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


require APPPATH.'/libraries/REST_Controller.php';
require APPPATH.'/libraries/Db_api.php';

class Restdb extends Db_api {

	/**
	 * Index Page for this controller.
	 *
	 */
	public function index_get($user = null, $db = null, $table = null, $local = null)
	{
				
		if (empty($db)) 	$db = $this->input->get('db', TRUE);
		if (empty($table)) 	$table = $this->input->get('table', TRUE);		
		if (empty($user)) 	$user = $this->input->get('user', TRUE);		
		if (empty($local)) 	$local = $this->input->get('local', TRUE);		
		
		// if we don't have a request send them to the upload page
		if(empty($db)) redirect('/upload');
		
		
		if (!empty($local) && $local == 'true') {
			
			$query = $this->get_database($user, $db);			
									
			if ($query->num_rows() > 0) {

			   	$db_settings = $query->row(0);

				$db_path = $_SERVER['DOCUMENT_ROOT'] . '/uploads/db/' . $db_settings->name_hash . '.db';				

				$table_blacklist = (!empty($table_blacklist)) ? explode(',', $db_settings->table_blacklist) : array();
				$column_blacklist = (!empty($column_blacklist)) ? explode(',', $db_settings->column_blacklist) : array();								

				$config = array($db_settings->name_url => array(
															'name' 				=> $db_path,
															'username' 			=> $db_settings->db_username,
															'password' 			=> $db_settings->db_password,
															'server' 			=> $db_settings->db_server,
															'port' 				=> $db_settings->db_port,
															'type' 				=> $db_settings->type,
															'table_blacklist' 	=> $table_blacklist,
															'column_blacklist' 	=> $column_blacklist));				
			}			
			
				
		
		} else {
			
			$query = $this->get_database($user, $db);					
			
			if(empty($table)) {
				return $this->show_docs($query->first_row('array'));
			}						
						
			if ($query->num_rows() > 0) {						
				
			   	$db_settings = $query->row(0);
			
				$table_blacklist = (!empty($table_blacklist)) ? explode(',', $db_settings->table_blacklist) : array();
				$column_blacklist = (!empty($column_blacklist)) ? explode(',', $db_settings->column_blacklist) : array();								
			
				$config = array($db_settings->name_url => array(
															'name' 				=> $db_settings->db_name,
															'username' 			=> $db_settings->db_username,
															'password' 			=> $db_settings->db_password,
															'server' 			=> $db_settings->db_server,
															'port' 				=> $db_settings->db_port,
															'type' 				=> $db_settings->type,
															'table_blacklist' 	=> $table_blacklist,
															'column_blacklist' 	=> $column_blacklist));				
			}
			
			
			
		} 
		
		$this->register_db( $db, $config );		
		//$this->register_custom_sql( 'democracymap', config_item('sql_args') );		
		
		$query = array('db' => $db, 'table' => $table);
		$query = $this->parse_query($query);
		$this->set_db( $query['db'] );
		$results = $this->query( $query );
		
		$this->response($results, 200);
	}
	
	
	
	
	
	
	public function dashboard_get($user = null) {
		
			
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
			
			
			$this->load->view('user_view', $data);
		
		
	}
	
	
	public function show_docs($db_config) {
		
		$data = array('db' => $db_config);
		
		$this->load->view('docs_view', $data);
		
		
	}	
	
	
	private function get_database($user_url, $name_url = null) {
		
		$query = array('user_url' => $user_url);		

		if (!empty($name_url)) {
			$query['name_url'] = $name_url;		
		}
				
		return $this->db->get_where('db_connections', $query);				
		
	}
	
	
	private function get_user($user_url) {
		
		$query = array('name_url' => $user_url);		
				
		return $this->db->get_where('users_auth', $query);				
		
	}	
	
	public function add_get() {
		
		$this->load->view('add_view');
		
	}	
	
	
	public function add_post() {
		
		if (empty($user) && !$this->session->userdata('username')) {	
			redirect('login');
		}		
						
		$name_url = $this->slugify($this->input->post('db_name', TRUE));
				
		
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
