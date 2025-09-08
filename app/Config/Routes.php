<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

<<<<<<< HEAD
$routes->get('/', 'Auth::login');
=======
$routes->get('/', 'Home::index');
>>>>>>> 064e4f59a89e4f96ebf3c58f1700be8c6edf7665
$routes->get('/login', 'Auth::login');
$routes->post('/login', 'Auth::attemptLogin');
$routes->get('/logout', 'Auth::logout');
$routes->get('/dashboard', 'Dashboard::index');
<<<<<<< HEAD
=======
// Default and Home routes
$routes->get('/', 'Home::index');
>>>>>>> 064e4f59a89e4f96ebf3c58f1700be8c6edf7665
$routes->get('/home', 'Home::index');
// Register routes
// Registration disabled

