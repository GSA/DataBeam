<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Swagger_model extends CI_Model {

	var $apiVersion 	= NULL;
	var $swaggerVersion	= NULL;
	var $basePath	 	= NULL;
	var $resourcePath 	= NULL;
	var $apis 			= array();


	public function __construct(){
		parent::__construct();
		
		$allowablevalues 	= array($this->allowablevalues());
		$parameters 		= array($this->parameters($allowablevalues));
		
		$errorResponses		= array($this->errorResponses());
		$operations			= array($this->operations($parameters, $errorResponses));
		
		$this->apis			= array($this->api($operations));

	}
	
	public function api($operations = null) {
		
		$api = array(
			'path' 			=> NULL, 
			'description' 	=> NULL, 
			'operations' 	=> $operations
		);
		
		return $api;
		
	}
	
	public function operations($parameters = null, $errorResponses = null) {
		
		$operations = array(
			'httpMethod' 		=> NULL, 
			'nickname' 			=> NULL, 
			'responseClass' 	=> NULL,
			'parameters'  		=> $parameters, 
			'summary' 			=> NULL, 
			'notes' 			=> NULL, 
			'errorResponses' 	=> $errorResponses
		);
		
		return $operations;
		
	}	
	
	public function parameters($allowablevalues = null) {
		
		$parameters = array(
			'paramType' 		=> NULL, 
			'name' 				=> NULL, 
			'description' 		=> NULL,
			'dataType' 			=> NULL, 
			'required'			=> NULL, 
			'allowableValues'  	=> $allowablevalues, 
			'allowMultiple' 	=> NULL
		);
		
		return $parameters;
		
	}	
	
	public function allowablevalues() {
		
		$allowablevalues = array(
			'min' 				=> NULL, 
			'max' 				=> NULL, 
			'valueType' 		=> NULL
		);
		
		return $allowablevalues;
		
	}	
	
	public function errorResponses() {
		
		$errorResponses = array(
			'code'		=> NULL, 
			'reason'	=> NULL
		);
		
		return $errorResponses;
		
	}	
	
	
	

}

?>