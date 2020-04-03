<?php namespace App\Controllers;

use App\Controllers\BaseController;
use \Firebase\JWT\JWT;

class App extends BaseController
{
	public function index()
	{
      $request = \Config\Services::request();
			echo '<pre>';
			print_r($request->getHeaderLine('origin'));
			echo '</pre>';
	}
}
