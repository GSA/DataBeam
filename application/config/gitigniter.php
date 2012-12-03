<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  GitIgniter Config
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

//Your application's client ID
$config['gh_client_id'] = '';

//Your application's secret
$config['gh_secret'] = '';

//Github's auth URL
$config['gh_auth_url'] = 'https://github.com/login/oauth/authorize';

//Github's access token URL
$config['gh_token_url'] = 'https://github.com/login/oauth/access_token';

//Github's API endpoint URL
$config['gh_api_url'] = 'https://api.github.com/';

//Controller/action responsible for accepting the temporary code (you should also place the process_code() call in this controller
$config['gh_redir_url'] = 'test/index'; //controller/action

//Comma separated list of scope options (leave blank for public access only)
$config['gh_scope'] = 'user,public_repo,repo,gist';

//DB table used to store Github token
$config['gh_table']	= 'meta';

//DB field used to match the user in the DB table
$config['gh_where_field'] = 'user_id';

//DB field used to store the Github token
$config['gh_token_field'] = 'gh_token';

//Optional. If you need to create repos for an organization.
//$config['gh_org'] = ''; 


/* End of file gitigniter.php */
/* Location: ./application/config/github.php */