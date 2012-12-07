<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


require APPPATH.'/libraries/REST_Controller.php';
require APPPATH.'/libraries/Db_api.php';

class Restdb extends Db_api {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -  
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in 
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	public function index_get($db = null, $user = null, $table = null)
	{
				
		if (empty($db)) 	$db = $this->input->get('db', TRUE);
		if (empty($table)) 	$table = $this->input->get('table', TRUE);		
		if (empty($user)) 	$user = $this->input->get('user', TRUE);		
		if (empty($upload)) $upload = $this->input->get('upload', TRUE);		
		
		// if we don't have a request send them to the upload page
		if(empty($db)) redirect('/upload');
		
		
		if (!empty($upload) && $upload == 'true') {
			$db_path = $_SERVER['DOCUMENT_ROOT'] . '/uploads/db/' . $db . '.db';
			
			$config = array($db => array( 
			            							'name' => $db_path,
			            							'username' => '',
			            							'password' => '',
			            							'server' => '',
			            							'port' => '',
			            							'type' => 'sqlite',
			            							'table_blacklist' => array(),
			            							'column_blacklist' => array()));		
		
		} else {
			
			$query = $this->get_database($user, $db);
						
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
			//$config = config_item('args');
		} 
		
		$this->register_db( $db, $config );		
		//$this->register_custom_sql( 'democracymap', config_item('sql_args') );		
		
		$query = array('db' => $db, 'table' => $table);
		$query = $this->parse_query($query);
		$this->set_db( $query['db'] );
		$results = $this->query( $query );
		
		$this->response($results, 200);
	}
	
	
	private function get_database($user_url, $name_url) {
				
		return $this->db->get_where('db_connections', array('user_url' => $user_url, 'name_url' => $name_url));				
		
	}

	public function router_get($user_url = null, $name_url = null, $table_name = null) {								
				
		$this->index_get($name_url, $user_url, $table_name);		
						
	}
	
	
	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */