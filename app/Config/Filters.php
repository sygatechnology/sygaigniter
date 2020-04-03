<?php namespace Config;

use CodeIgniter\Config\BaseConfig;

class Filters extends BaseConfig
{
    /**
     * Constructor
     * Initialize plugin filters
     */
    public function __construct(){
        $this->parseFilters();
    }

	// Makes reading things below nicer,
	// and simpler to change out script that's used.
	public $aliases = [
		'csrf'     => \CodeIgniter\Filters\CSRF::class,
		'toolbar'  => \CodeIgniter\Filters\DebugToolbar::class,
        'honeypot' => \CodeIgniter\Filters\Honeypot::class,
        'authentication' => \Syga\Filters\ApiAuth::class,
	];

	// Always applied before every request
	public $globals = [
		'before' => [
			//'honeypot'
			// 'csrf',
		],
		'after'  => [
			'toolbar',
			//'honeypot'
		],
	];

	// Works on all of a particular HTTP method
	// (GET, POST, etc) as BEFORE filters only
	//     like: 'post' => ['CSRF', 'throttle'],
	public $methods = [];

	// List filter aliases and any before/after uri patterns
	// that they should run on, like:
	//    'isLoggedIn' => ['before' => ['account/*', 'profiles/*']],
    public $filters = [];
    

    // Load custom filters from plugin filter files
    private function parseFilters(){
        global $_ENABLED_PLUGINS;
        foreach ($_ENABLED_PLUGINS as $plugin) {
            if(file_exists(PLUGINS_PATH . $plugin['dir'] . DIRECTORY_SEPARATOR . 'Filters.php')){
                require PLUGINS_PATH  . $plugin['dir'] . DIRECTORY_SEPARATOR . 'Filters.php';
                $class = '\Plugin\\' . $plugin['dir'] . '\\Filters';
                $Hooks = new $class();
                if( is_subclass_of( $Hooks, '\Syga\\Config\\Hooks' ) ){
                    $this->aliases = $Hooks->setAliases($this->aliases);
                    $this->globals = $Hooks->setGlobals($this->globals);
                    $this->methods = $Hooks->setMethods($this->methods);
                    $this->filters = $Hooks->setFilters($this->filters);
                } 
            }
        }
    }
}
