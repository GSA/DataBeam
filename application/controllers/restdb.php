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
		
		
		$query = $this->parse_query();
		$this->set_db( $query['db'] );
		$results = $this->query( $query );
        //
		//$renderer = 'render_' . $query['format'];
		//$this->$renderer( $results, $query );		
		
		//$this->load->view('welcome_message');
		
		$this->response($results, 200);
	}
	
	
	



	
	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */