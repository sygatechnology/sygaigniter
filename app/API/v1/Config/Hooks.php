<?php namespace Syga\Config;

class Hooks {

    public function __construct(){
        helper('array');
    }

    // Makes reading things below nicer,
	// and simpler to change out script that's used.
	public $aliases = [];

	// Always applied before every request
	public $globals = [
		'before' => [],
		'after'  => [],
	];

	// Works on all of a particular HTTP method
	// (GET, POST, etc) as BEFORE filters only
	//     like: 'post' => ['CSRF', 'throttle'],
	public $methods = [];

	// List filter aliases and any before/after uri patterns
	// that they should run on, like:
	//    'isLoggedIn' => ['before' => ['account/*', 'profiles/*']],
    public $filters = [];
    
	public function setAliases($aliases)
	{
		return array_merge_unique($aliases, $this->aliases);
    }
    
    public function setGlobals($globals)
	{
        if(! isset($this->globals['before']) || ! isset($this->globals['after'])) {
            $this->globals = [
                'before' => [],
                'after'  => [],
            ];
        }
        $globals['before'] = array_merge_unique($globals['before'], $this->globals['before']);
        $globals['after'] = array_merge_unique($globals['after'], $this->globals['after']);
		return $globals;
    }
    
    public function setMethods($methods)
	{
		return array_merge_unique($methods, $this->methods);
    }
    
    public function setFilters($filters)
	{
		return array_merge_unique($filters, $this->filters);
    }
    
}