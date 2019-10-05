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
$route['default_controller'] = 'login';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
$route['user_reg/(:any)'] = 'user_reg/index/$1';

$route['supervisor-log']='login/dashboard';
$route['view-transaction']='login/view_transaction';
$route['plant-details']='login/user_list';
$route['insert-coupons']='login/insert_coupons';
$route['add-coupon']='login/add_coupon';
$route['update-coupon']='login/update_coupon';
$route['delete-coupon']='login/delete_coupon';
$route['trip-cancel']='login/trip_cancel';
$route['insert-subscription']='login/insert_subscription';
$route['insert-tripcancel']='login/insert_tripcancel';
$route['reporting']='login/rider_document';
$route['rider-list']='login/rider_list';
$route['admin-monitoring']='login/admin_monitoring';
$route['login-gethint']='login/gethint';


//$route['heat-map']='dashboard/heat_map';
$route['rider-catalogue']='dashboard/rider_catalogue';
//$route['rider-subscribtionList']='dashboard/rider_subscribtionList';
//$route['emergency-notify']='dashboard/emergency_notify';// 24-July-18 New Requirement
$route['emissions-inspections']='dashboard/total_amount';
$route['issues']='dashboard/total_kilometers';
$route['daily-instructions']='dashboard/user_attempts';
$route['user-failedlist']='dashboard/user_failedlist';
$route['plant-shift-log']='dashboard/rider_failedlist';
$route['view-riderGroup']="dashboard/view_riderGroup";
$route['single-groups/(:any)']="dashboard/single_groups";
