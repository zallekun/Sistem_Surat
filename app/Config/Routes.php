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
    $routes->post('profile/change-password', 'AuthController::updateProfile');
    
    // Surat Routes
    $routes->group('surat', function($routes) {
        $routes->get('', 'SuratController::index');
        $routes->get('create', 'SuratController::create', ['filter' => 'role:admin_prodi']);
        $routes->post('store', 'SuratController::store', ['filter' => 'role:admin_prodi']);
        $routes->get('(:num)', 'SuratController::show/$1');
        $routes->get('(:num)/edit', 'SuratController::edit/$1');
        $routes->post('(:num)/update', 'SuratController::update/$1');
        $routes->post('(:num)/submit', 'SuratController::submit/$1');
        $routes->post('bulk-submit', 'SuratController::bulkSubmit', ['filter' => 'role:admin_prodi']);
    });
    
    // Workflow Routes
    $routes->group('workflow', function($routes) {
        $routes->post('approve/(:num)', 'WorkflowController::approve/$1');
        $routes->post('reject/(:num)', 'WorkflowController::reject/$1');
        $routes->post('revise/(:num)', 'WorkflowController::revise/$1');
        $routes->post('dispose/(:num)', 'WorkflowController::dispose/$1');
        $routes->post('complete/(:num)', 'WorkflowController::complete/$1');
        $routes->get('history/(:num)', 'WorkflowController::history/$1');
        $routes->get('timeline/(:num)', 'WorkflowController::timeline/$1');
    });
    
    // File Management Routes
    $routes->group('file', function($routes) {
        $routes->get('upload/(:num)', 'FileController::uploadForm/$1');
        $routes->post('upload/(:num)', 'FileController::upload/$1');
        $routes->get('download/(:num)', 'FileController::download/$1');
        $routes->get('preview/(:num)', 'FileController::preview/$1');
        $routes->post('delete/(:num)', 'FileController::delete/$1');
        $routes->get('history/(:num)', 'FileController::history/$1');
    });
    
    // Notification Routes
    $routes->group('notifications', function($routes) {
        $routes->get('', 'NotificationController::index');
        $routes->get('recent', 'NotificationController::getRecent');
        $routes->post('mark-read/(:num)', 'NotificationController::markAsRead/$1');
        $routes->post('mark-read', 'NotificationController::markAsRead');
        $routes->delete('(:num)', 'NotificationController::delete/$1');
        $routes->get('settings', 'NotificationController::settings');
        $routes->post('settings', 'NotificationController::updateSettings');
        $routes->post('test', 'NotificationController::testNotification');
    });
    
    // Analytics Routes (Management only)
    $routes->group('analytics', function($routes) {
        $routes->get('', 'AnalyticsController::index');
        $routes->get('reports', 'AnalyticsController::reports');
        $routes->get('export/pdf', 'AnalyticsController::exportPDF');
        $routes->get('api/chart/(:segment)', 'AnalyticsController::getChartData/$1');
    });
    
    // Advanced Search Routes
    $routes->group('search', function($routes) {
        $routes->get('', 'SearchController::index');
        $routes->get('suggestions', 'SearchController::suggestions');
        $routes->post('save', 'SearchController::saveSearch');
        $routes->delete('saved/(:num)', 'SearchController::deleteSavedSearch/$1');
        $routes->get('analytics', 'SearchController::analytics', ['filter' => 'role:dekan,kabag_tu,admin_prodi']);
    });
    
    // Approval Routes
    $routes->group('approval', ['filter' => 'role:staff_umum,kabag_tu,dekan,wd_akademik,wd_kemahasiswa,wd_umum,kaur_keuangan'], function($routes) {
        $routes->get('pending', 'ApprovalController::pending');
        $routes->get('completed', 'ApprovalController::completed');
    });
});
