<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  GitIgniter Model
*
* Author:  Nic Rosental
* 		   nic@epiclabs.com
*	  	   @nicdev
*
* Created:  10.31.2011
*
*/

class Gitigniter_model extends CI_Model {

	protected $table;
	protected $where_field;
	protected $token_field;


	/*********************************************************************************************************************
	
	Constructor
	Make sure you've set up the config file, or this will be an exercise in futility.	
	
	*********************************************************************************************************************/

	public function __construct(){
		parent::__construct();

		$this->load->database();
		$this->load->config('gitigniter', TRUE);
		
		$this->table = $this->config->item( 'gh_table', 'gitigniter' );
		$this->where_field = $this->config->item( 'gh_where_field', 'gitigniter' );
		$this->token_field = $this->config->item( 'gh_token_field', 'gitigniter' );

	}

	/*********************************************************************************************************************
	
	Store/update token	
	
	*********************************************************************************************************************/
	
	public function store_token( $token, $user_id ){
	
		if( empty( $this->table ) || empty( $this->where_field ) || empty( $this->token_field ) )
		{
		
			return FALSE;
		
		}
		else
		{
			
			$update_data = array( $this->token_field => $token );
			
			return $this->db->where( $this->where_field, $user_id )->update( $this->table, $update_data );
		
		}
	
	}
	
	/*********************************************************************************************************************
	
	Retrieve token
	
	*********************************************************************************************************************/
	
	public function retrieve_token( $user_id = NULL ){
	
		if( $user_id === NULL )
		{
		
			return FALSE;
		
		}
		else
		{
		
			return $this->db->select( $this->token_field )->where( $this->where_field, $user_id )->from( $this->table )->get()->row();
		
		}
	
	}
		
}