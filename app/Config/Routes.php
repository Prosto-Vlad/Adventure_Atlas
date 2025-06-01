<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/gallery', 'GalleryController::index');

$routes->group('auth', function ($routes) {
    $routes->post('login', 'Auth::login');
    $routes->post('register', 'Auth::register');
    $routes->post('logout', 'Auth::logout');
});

$routes->post('generate-world-map', 'MapController::generateWorldMap');
$routes->post('generate-city-map', 'MapController::generateCityMap');
$routes->post('generate-location-map', 'MapController::generateLocationMap');

$routes->group('map', function ($routes) {
    $routes->post('save', 'MapController::saveMap');
    $routes->post('update/(:num)', 'MapController::updateMap/$1');
    $routes->get('delete/(:num)', 'MapController::deleteMap/$1');
    $routes->get('view/(:num)', 'MapController::viewMap/$1');
    $routes->get('view-temp', 'MapController::viewTempMap');
});

