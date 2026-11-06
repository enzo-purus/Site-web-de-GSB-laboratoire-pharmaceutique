<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Controleur::index');
$routes->post('postdata', 'Controleur::index');
$routes->get('getdata', 'Controleur::index');
$routes->post('getdata', 'Controleur::index');