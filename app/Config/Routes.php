<?php namespace Config;

/**
 * --------------------------------------------------------------------
 * URI Routing
 * --------------------------------------------------------------------
 * This file lets you re-map URI requests to specific controller functions.
 *
 * Typically there is a one-to-one relationship between a URL string
 * and its corresponding controller class/method. The segments in a
 * URL normally follow this pattern:
 *
 *    example.com/class/method/id
 *
 * In some instances, however, you may want to remap this relationship
 * so that a different class/function is called than the one
 * corresponding to the URL.
 */

// Create a new instance of our RouteCollection class.
$routes = Services::routes(true);

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php'))
{
	require SYSTEMPATH . 'Config/Routes.php';
}

/**
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 * The RouteCollection object allows you to modify the way that the
 * Router works, by acting as a holder for it's configuration settings.
 * The following methods can be called on the object to modify
 * the default operations.
 *
 *    $routes->defaultNamespace()
 *
 * Modifies the namespace that is added to a controller if it doesn't
 * already have one. By default this is the global namespace (\).
 *
 *    $routes->defaultController()
 *
 * Changes the name of the class used as a controller when the route
 * points to a folder instead of a class.
 *
 *    $routes->defaultMethod()
 *
 * Assigns the method inside the controller that is ran when the
 * Router is unable to determine the appropriate method to run.
 *
 *    $routes->setAutoRoute()
 *
 * Determines whether the Router will attempt to match URIs to
 * Controllers when no specific route has been defined. If false,
 * only routes that have been defined here will be available.
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('App');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override('App\Controllers\App::show404');
$routes->setAutoRoute(true);

/**
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.

$app = new \Config\App();

$routes->get('auth/signin', '\App\Controllers\Api\Auth::authenticate');
$routes->post('auth/signin', '\App\Controllers\Api\Auth::authenticate');
$routes->get('auth/validate', '\App\Controllers\Api\Auth::activeAccount');
$routes->group($app->apiRouteEndpoint, ['namespace' => '\App\Controllers\Api', 'filter' => 'authentication'], function($routes)
{
		$routes->get('plugins', 'Plugins::index');
		$routes->get('plugins/(:num)', 'Plugins::index/$1');
		$routes->get('plugins/(:num)/(:num)', 'Plugins::index/$1/$2');
		$routes->post('plugins', 'Plugins::install');
		$routes->put('plugins', 'Plugins::activateDeactivate');
		$routes->delete('plugins/(:any)', 'Plugins::delete/$1');

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

$plugins = \App\Services\PluginsService::get('enabled', 0);
global $_ENABLED_PLUGINS;
$_ENABLED_PLUGINS = $plugins['data'];
foreach ($plugins['data'] as $plugin) {
	if(file_exists(PLUGINS_PATH . $plugin['dir'] . '/Routes.php')){
		$routes->apiPluginDir = $plugin['dir'];
		$routes->group('api', ['namespace' => '\App\Plugins\\'.$plugin['dir'].'\Controllers'], function($routes)
		{
			require PLUGINS_PATH  . $routes->apiPluginDir . '/Routes.php';
		});
	}
}

/**
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need to it be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php'))
{
	require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}


