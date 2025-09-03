<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Redirect root to login
$routes->get('/', function() {
    return redirect()->to('/login');
});

// Authentication Routes
$routes->group('', [], function($routes) {
    $routes->get('login', 'AuthController::login');
    $routes->post('auth/authenticate', 'AuthController::authenticate');
    $routes->get('logout', 'AuthController::logout');
});

// Protected Routes (require authentication)
$routes->group('', ['filter' => 'auth'], function($routes) {
    $routes->get('dashboard', 'DashboardController::index');
    $routes->get('profile', 'AuthController::profile');
    $routes->post('profile/update', 'AuthController::updateProfile');
});
