<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

/**
* Name:  GitIgniter Class
* 
* Author: Nic Rosental
* 	  nic@epiclabs.com
*         @nicdev
*          
* Location: https://github.com/nicdev/GitIgniter
*          
* Created:  10.28.2011
* 
* Description:  Library to use Github API, including authentication.
* 
*/


class Gitigniter {
	
	protected $ci;
	protected $client_id;
	protected $secret;
	protected $auth_url;
	protected $token_url;
	protected $api_url;
	protected $org;
	
	public function __construct(){
		
		$this->ci =& get_instance();
		$this->ci->load->config('gitigniter', TRUE);
		
		$this->client_id = $this->ci->config->item( 'gh_client_id', 'gitigniter' );
		$this->secret = $this->ci->config->item( 'gh_secret', 'gitigniter' );
		$this->api_url = $this->ci->config->item( 'gh_api_url', 'gitigniter' );
		$this->auth_url = $this->ci->config->item( 'gh_auth_url', 'gitigniter' );
		$this->token_url = $this->ci->config->item( 'gh_token_url', 'gitigniter' );
		$this->org = $this->ci->config->item( 'gh_org', 'gitigniter' );
		$this->redir_url = $this->ci->config->item( 'gh_redir_url', 'gitigniter' );
		$this->scope = $this->ci->config->item( 'gh_scope', 'gitigniter' );
		
		$this->ci->load->model('gitigniter_model');
		$this->ci->load->library( 'session' );
		$this->ci->load->helper( 'url' );
		//$this->ci->load->spark( 'curl/1.2.0' );
	
	}
	
	/*********************************************************************************************************************
	
	Authorize with Github's OAuth
	
	*********************************************************************************************************************/
	
	public function authorize_request(){
	
		$url = $this->auth_url . '?client_id=' . $this->client_id;
				
		if( ! empty( $this->scope ) )
		{
		
			$url .= '&scope=' . $this->scope;
		
		}
		
		if( ! empty( $this->redir_url ) )
		{
		
			$url .= '&redirect_uri=' . site_url( $this->redir_url );
		
		}
		
		redirect ( $url );
					
	}
	
			
	/*********************************************************************************************************************
	
	Process temporary code
	@return access token. Store it folks!
	
	*********************************************************************************************************************/
	
	public function process_code( $code = NULL ){		

		//Process temp code provided by Github and request token
		if( ! isset( $code ) )
		{
			
			$code = $_GET['code']; //It's nicer when they pass it to the method, but oh well.
		
			
		}

		$ch = curl_init( $this->token_url );
			
		$post = array(
						'client_id' => $this->client_id,
						'redirect_uri' => site_url( $this->redir_url ),
						'client_secret' => $this->secret,
						'code' => $code				 
		);

		curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Authorization: token ' . $this->ci->session->userdata( 'gh_token' ) ) );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, TRUE );
		curl_setopt( $ch, CURLOPT_POST, TRUE );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $post );
		
		$response =  curl_exec( $ch );
		
		//@todo change this crap for a regex

		$access_token = explode( '&', $response );
		$access_token = explode( '=', $access_token[0] );
		$access_token = $access_token[1];
		
		return $access_token;
		
	}	
	
	/*********************************************************************************************************************
	
	Store access token according to options	
	
	*********************************************************************************************************************/
	
	public function store_token( $token, $user_id, $session = TRUE, $db = TRUE ){
	
		if( $session === TRUE)
		{
		
			$this->ci->session->set_userdata( 'gh_token', $token );
					
		}
		
		if( $db === TRUE )
		{
		
			$this->ci->gitigniter_model->store_token( $token, $user_id );
		
		}
	
	}
	
	
	/*********************************************************************************************************************
	
	General API get call	
	
	*********************************************************************************************************************/
	
	public function get_call( $call, $options = NULL ){
	
		$query_string = ( is_array( $options ) ) ? http_build_query( $options ) : '';
		
		$ch = curl_init( $this->api_url . $call . '?' . $query_string );
		
		curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Authorization: token ' . $this->ci->session->userdata( 'gh_token' ) ) );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, TRUE );

		$response = curl_exec( $ch );
		
		$errors = curl_error( $ch );
		
		curl_close( $ch );
		
		if( ! empty ( $errors ) )
		{
		
			return $errors;
		
		}

		return json_decode( $response );
	
	}
	
	/*********************************************************************************************************************
	
	General API post call
	
	*********************************************************************************************************************/
	
	public function post_call( $call, $options = NULL ){
	
		$post_options = json_encode( $options );
		
		$ch = curl_init( $this->api_url . $call );
		
		curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Authorization: token ' . $this->ci->session->userdata( 'gh_token' ) ) );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, TRUE );
		curl_setopt( $ch, CURLOPT_POST, TRUE );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $post_options );
		
		$response = curl_exec( $ch );
		
		$errors = curl_error( $ch );
		
		curl_close( $ch ); //These are errors pertaining actual connection and HTTP errors
		
		if( ! empty ( $errors ) )
		{
		
			return $errors;
		
		}
		
		return json_decode( $response ); //This means there was successful access to the API, but the result could be errorneous.
		
	}
	
	/*********************************************************************************************************************
	
	Retrieve access token for a determined user. As a freebie it gets added to the session
	
	*********************************************************************************************************************/
	
	public function retrieve_token( $user_id ){
	
		$token =  $this->ci->gitigniter_model->retrieve_token( $user_id );
		
		$this->ci->session->set_userdata( 'gh_token', $token->gh_token );
		
		return $token->gh_token;
		
	}
	
}
	


/* End of file Github.php */