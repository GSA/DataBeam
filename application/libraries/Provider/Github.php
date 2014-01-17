<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * GitHub OAuth2 Provider
 *
 * @package    CodeIgniter/OAuth2
 * @category   Provider
 * @author     Phil Sturgeon
 * @copyright  (c) 2012 HappyNinjas Ltd
 * @license    http://philsturgeon.co.uk/code/dbad-license
 */

class OAuth2_Provider_Github extends OAuth2_Provider
{
	public function url_authorize()
	{
		return 'https://github.com/login/oauth/authorize';
	}

	public function url_access_token()
	{
		return 'https://github.com/login/oauth/access_token';
	}

	public function get_user_info(OAuth2_Token_Access $token)
	{
		$url = 'https://api.github.com/user?'.http_build_query(array(
			'access_token' => $token->access_token,
		));

        $options  = array('http' => array('user_agent'=> $_SERVER['HTTP_USER_AGENT']));
        $context  = stream_context_create($options);

		$user = json_decode(file_get_contents($url, false, $context));

		// Create a response from the request
		return array(
			'uid' => $user->id,
			'nickname' => $user->login,
			'name' => $user->name,
			'email' => $user->email,
			'urls' => array(
			  'GitHub' => 'http://github.com/'.$user->login,
			  'Blog' => $user->blog,
			),
		);
	}
}