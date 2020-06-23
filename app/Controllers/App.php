<?php namespace App\Controllers;

use App\Controllers\BaseController;
use App\Entities\User;
use \Firebase\JWT\JWT;

class App extends BaseController
{
	public function index()
	{
		$user = new User(4);
		echo '<pre>';
		print_r($user->getRoles());
		echo '</pre>';
	}
}
