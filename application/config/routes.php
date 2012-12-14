<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

$route['default_controller'] = "welcome";
$route['404_override'] = '';

$route['login'] = "auth/session/github";
$route['logout'] = "auth/logout";

$route['auth/session/(:any)'] = "auth/session/$1";
$route['(:any)/local/(:any)'] = "restdb/router_local/$1/$2"; // 1: user_url, 3: name_url

$route['(:any)/api-docs'] = "restdb/swagger/$1"; // 1: user_url
$route['(:any)/api-docs(:any)/(:any)'] = "restdb/swagger/$1/$3"; // 1: user_url, 2: name_url,
$route['(:any)/api-docs(:any)'] = "restdb/swagger/$1"; // 1: user_url

$route['(:any)/(:any)/(:any)'] = "restdb/router/$1/$2/$3"; // 1: user_url, 2: name_url, 3: table name

$route['upload'] = "upload";
$route['upload/upload_file'] = "upload/upload_file";

$route['new'] = "restdb/add";

$route['dashboard'] = "restdb/dashboard";

$route['(:any)/(:any)'] = "restdb/router/$1/$2"; // 1: user_url, 2: name_url,
$route['(:any)'] = "restdb/dashboard/$1";



/* End of file routes.php */
/* Location: ./application/config/routes.php */