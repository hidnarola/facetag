<?php

defined('BASEPATH') OR exit('No direct script access allowed');

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
  |	https://codeigniter.com/user_guide/general/routing.html
  |
  | -------------------------------------------------------------------------
  | RESERVED ROUTES
  | -------------------------------------------------------------------------
  |
  | There are three reserved routes:
  |
  |	$route['default_controller'] = 'welcome';
  |
  | This route indicates which controller class should be loaded if the
  | URI contains no data. In the above example, the "welcome" class
  | would be loaded.
  |
  |	$route['404_override'] = 'errors/page_missing';
  |
  | This route will tell the Router which controller/method to use if those
  | provided in the URL cannot be matched to a valid route.
  |
  |	$route['translate_uri_dashes'] = FALSE;
  |
  | This is not exactly a route, but allows you to automatically route
  | controller and method names that contain dashes. '-' isn't a valid
  | class or method name character, so it requires translation.
  | When you set this option to TRUE, it will replace ALL dashes in the
  | controller and method URI segments.
  |
  | Examples:	my-controller/index	-> my_controller/index
  |		my-controller/my-method	-> my_controller/my_method
 */
$route['default_controller'] = 'home';

//--Admin route
$route['admin'] = 'admin/login';
$route['admin_logout'] = 'admin/login/logout'; //--- Logout route
$route['admin/change_password'] = 'admin/home/change_password'; //--- Change password route
$route['admin/businesses/add'] = 'admin/businesses/edit'; //--- Add business route 
$route['admin/businesses/edit_icp/(:any)/(:any)'] = 'admin/businesses/add_icp/$1/$2'; //--- Edit ICP route
$route['admin/cart/view/(:any)/(:any)'] = 'admin/orders/cart_item_view/$1/$2'; //--- cart order view
$route['admin/hotels/add/(:any)'] = 'admin/hotels/edit/$1'; //--- Edit Hotel route
//--Business route
$route['business/profile'] = 'business/home/profile'; //-- Business Profile route
$route['business/private_information'] = 'business/home/private_information'; //-- Business Profile route
$route['business/promo_images'] = 'business/home/promo_images'; //-- Business Promo Feature Images route
$route['business/change_password'] = 'business/home/change_password'; //--- Change password route
$route['business/account_profile'] = 'business/home/account_profile'; //--- Update Profile route
$route['business/icps/add'] = 'business/icps/edit'; //--- Add Icp route 
$route['business/hotels/add/(:any)'] = 'business/hotels/edit/$1'; //--- Edit Hotel route
$route['legal/terms'] = 'home/terms'; //--- Terms route
$route['legal/appterms'] = 'home/appterms'; //--- App Terms route
$route['legal/privacy'] = 'home/privacy'; //--- Privacy route
$route['set_password'] = 'reset_password/set_password'; //--- Set password route
$route['logout'] = 'login/logout'; //--- Logout route
$route['facebook/connect'] = 'fb/connect';
$route['facebook/callback'] = 'fb/callback';
$route['facebook/post_images'] = 'fb/post_images';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
