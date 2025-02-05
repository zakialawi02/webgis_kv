<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (is_file(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Kafe');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(true);
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
// $routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Kafe::index');
$routes->get('/noaccess', 'Kafe::noaccess');
$routes->match(['get', 'post'], 'admin/getDataAjaxRemote', 'Admin::getDataAjaxRemote');

$routes->get('/map', 'Kafe::map');
$routes->get('/data', 'Kafe::data');
$routes->get('/contact', 'Kafe::contact');
$routes->get('/about', 'Kafe::about');
$routes->get('/dashboard', 'Admin::index', ['filter' => 'role:SuperAdmin,Admin,User']);
$routes->get('/admin/pending', 'Admin::pending', ['filter' => 'role:SuperAdmin,Admin']);

$routes->get('/admin/setting', 'Admin::setting', ['filter' => 'role:SuperAdmin,Admin']);

$routes->get('/admin/geojson', 'Admin::geojson', ['filter' => 'role:SuperAdmin,Admin']);
$routes->get('/admin/features', 'Admin::geojson', ['filter' => 'role:SuperAdmin,Admin']);
$routes->get('/admin/features/tambah', 'Admin::tambahGeojson', ['filter' => 'role:SuperAdmin,Admin']);
$routes->get('/admin/features/edit/(:num)', 'Admin::editGeojson/$1', ['filter' => 'role:SuperAdmin,Admin']);
$routes->delete('/admin/delete_Geojson/(:num)', 'Admin::delete_Geojson/$1', ['filter' => 'role:SuperAdmin,Admin']);

$routes->get('/admin/kafe', 'Admin::kafe', ['filter' => 'role:SuperAdmin,Admin']);
$routes->get('/admin/data/kafe', 'Admin::kafe', ['filter' => 'role:SuperAdmin,Admin']);
$routes->get('/admin/data/kafe/tambah', 'Admin::tambahKafe', ['filter' => 'role:SuperAdmin,Admin,User']);
$routes->get('/admin/data/kafe/edit/(:num)', 'Admin::editKafe/$1', ['filter' => 'role:SuperAdmin,Admin']);
$routes->get('/kafe/edit/(:num)', 'Admin::editKafe/$1', ['filter' => 'role:SuperAdmin,Admin,User']);
$routes->post('/admin/tambah_Kafe', 'Admin::tambah_Kafe');
$routes->post('/admin/addKafe', 'Admin::addKafe');

$routes->get('/user/manajemen', 'User::manajemen', ['filter' => 'role:SuperAdmin,Admin']);

$routes->get('/kafe/(:num)/detail', 'Kafe::detail/$1');

$routes->get('/api/submit/user/(:num)', 'Api::submit/$1');

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
