<?php
	$routes->get('api/auth/sigin', 'App\API\v1\Controllers\Auth::authenticate');
	$routes->get('api/auth/validate', 'App\API\v1\Controllers\Auth::activeAccount');
	$routes->group('api', ['namespace' => 'App\API\v1\Controllers', 'filter' => 'api-auth'], function($routes)
	{
			$routes->get('profile', 'Profile::index');
			$routes->put('profile', 'Profile::up');

			$routes->resource('roles');
			$routes->delete('roles/purge', 'Roles::purge');

			$routes->resource('users');
			$routes->delete('users/purge', 'Users::purge');

			$routes->resource('capabilities');
			$routes->delete('capabilities/purge', 'Capabilities::purge');

			$routes->get('auth/signout', 'Auth::logout');
	});

	$routes->group('core', ['namespace' => 'App\Controllers\Core', 'filter' => 'api-auth'], function($routes)
	{
			$routes->get('dashboard', 'Dashboard::index');
	});
