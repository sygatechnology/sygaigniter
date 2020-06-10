<?php
    // Product routes
    $routes->get('products', 'Posts::index');
    $routes->get('products/(:num)', 'Posts::show/$1');
    $routes->post('products', 'Posts::create', ['filter' => 'authentication']);
    $routes->put('products/(:num)', 'Posts::update/$1', ['filter' => 'authentication']);
    $routes->delete('products/(:any)', 'Posts::delete/$1', ['filter' => 'authentication']);
    
    // Member routes
    $routes->get('members', 'Members::index');
    $routes->get('members/(:num)', 'Members::show/$1');
    $routes->post('members', 'Members::create', ['filter' => 'authentication']);
    $routes->put('members/(:num)', 'Members::update/$1', ['filter' => 'authentication']);
    $routes->delete('members/(:any)', 'Members::delete/$1', ['filter' => 'authentication']);
    
