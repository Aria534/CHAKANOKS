<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Base route -> Login page
$routes->get('/', 'Auth::login');

// Auth
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::attemptLogin');
$routes->get('logout', 'Auth::logout');

// Dashboard (role-based dispatch)
$routes->get('dashboard', 'Dashboard::index');

// Dashboard routes (controller-backed where available)
$routes->get('dashboard/central', 'CentralDashboard::index');
$routes->get('dashboard/branch-manager', 'BranchController::dashboard');
$routes->get('dashboard/franchise', 'FranchiseDashboard::index');
$routes->get('dashboard/inventory', 'InventoryController::index');
$routes->get('dashboard/logistics', 'LogisticsDashboard::index');

// Logistics Dashboard Routes
$routes->get('logistics', 'LogisticsDashboard::index');
$routes->get('shipments', 'LogisticsDashboard::shipments');
$routes->get('routes', 'LogisticsDashboard::routes');


$routes->get('franchise', 'FranchiseDashboard::index');


// Optional Home
$routes->get('home', 'Home::index');

$routes->get('/branches', 'BranchController::index');
$routes->get('/products', 'ProductController::index');
$routes->get('/suppliers', 'SupplierController::index');
$routes->get('/suppliers/create', 'SupplierController::create');
$routes->post('/suppliers/store', 'SupplierController::store');
$routes->get('/suppliers/edit/(:num)', 'SupplierController::edit/$1');
$routes->post('/suppliers/update/(:num)', 'SupplierController::update/$1');
$routes->post('/suppliers/delete/(:num)', 'SupplierController::delete/$1');
$routes->get('/orders', 'OrderController::index');
$routes->get('/logout', 'Auth::logout');
$routes->get('/inventory', 'InventoryController::index');
$routes->post('/inventory/adjust', 'InventoryController::adjust');
$routes->get('/inventory/scan', 'InventoryController::scan');
$routes->post('/inventory/scan', 'InventoryController::processScan');

// Orders (use existing purchase_orders table for PR -> PO flow)
$routes->get('/orders/create', 'OrderController::create');
$routes->post('/orders/store', 'OrderController::store');

// Reports
$routes->get('reports', 'ReportsController::index');
$routes->get('reports/generate/(:any)', 'ReportsController::generate/$1');
$routes->post('/orders/(:num)/approve', 'OrderController::approve/$1');
$routes->post('/orders/(:num)/send', 'OrderController::send/$1');
$routes->post('/orders/(:num)/receive', 'OrderController::receive/$1');

// Manage Users
$routes->get('/users', 'CentralDashboard::manageUsers');
$routes->get('/users/create', 'CentralDashboard::createUser');
$routes->post('/users/create', 'CentralDashboard::createUser');
$routes->get('/users/edit/(:num)', 'CentralDashboard::editUser/$1');
$routes->post('/users/edit/(:num)', 'CentralDashboard::editUser/$1');
$routes->post('/users/delete/(:num)', 'CentralDashboard::deleteUser/$1');
