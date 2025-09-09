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

// Direct views for each dashboard (useful for testing)
$routes->get('dashboard/central', static fn() => view('dashboard/central_admin'));
$routes->get('dashboard/branch-manager', static fn() => view('dashboard/branch_manager'));
$routes->get('dashboard/franchise', static fn() => view('dashboard/franchise_manager'));
$routes->get('dashboard/inventory', static fn() => view('dashboard/inventory_staff'));
$routes->get('dashboard/logistics', static fn() => view('dashboard/logistics_coordinator'));

// Optional Home
$routes->get('home', 'Home::index');

$routes->get('/branches', 'BranchController::index');
$routes->get('/products', 'ProductController::index');
$routes->get('/orders', 'OrderController::index');
$routes->get('/logout', 'Auth::logout');
$routes->get('/inventory', 'InventoryController::index');



