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
	public function index_get()
	{
		
		//$this->register_db_api( 'democracymap', $args );		// moved this to the main library constructor
		
		if ($_REQUEST['upload'] == 'true') {
			$db_path = $_SERVER['DOCUMENT_ROOT'] . '/uploads/db/' . $_REQUEST['db'] . '.db';
			
			$config = array($_REQUEST['db'] => array( 
			            							'name' => $db_path,
			            							'username' => '',
			            							'password' => '',
			            							'server' => '',
			            							'port' => '',
			            							'type' => 'sqlite',
			            							'table_blacklist' => array(),
			            							'column_blacklist' => array()));		
		
		} else {
			$config = config_item('args');
		} 
		
		$this->register_db( $_REQUEST['db'], $config );		
		//$this->register_custom_sql( 'democracymap', config_item('sql_args') );		
		
		$query = $this->parse_query();
		$this->set_db( $query['db'] );
		$results = $this->query( $query );
		
		$this->response($results, 200);
	}
	
	
	



	
	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */