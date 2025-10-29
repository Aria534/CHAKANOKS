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
$routes->get('dashboard/branch-manager', 'BranchDashboard::index');
$routes->get('dashboard/franchise', 'FranchiseDashboard::index');
$routes->get('dashboard/inventory', 'InventoryController::index');
$routes->get('dashboard/logistics', 'LogisticsDashboard::index');

// Optional Home
$routes->get('home', 'Home::index');

$routes->get('/branches', 'BranchController::index');
$routes->get('/products', 'ProductController::index');
$routes->get('/orders', 'OrderController::index');
$routes->get('/logout', 'Auth::logout');
$routes->get('/inventory', 'InventoryController::index');
$routes->post('/inventory/adjust', 'InventoryController::adjust');
$routes->get('/inventory/scan', 'InventoryController::scan');
$routes->post('/inventory/scan', 'InventoryController::processScan');

// Orders (use existing purchase_orders table for PR -> PO flow)
$routes->get('/orders/create', 'OrderController::create');
$routes->post('/orders', 'OrderController::store');
$routes->post('/orders/(:num)/approve', 'OrderController::approve/$1');
$routes->post('/orders/(:num)/send', 'OrderController::send/$1');
$routes->post('/orders/(:num)/receive', 'OrderController::receive/$1');



