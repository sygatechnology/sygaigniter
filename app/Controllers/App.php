<?php namespace App\Controllers;

use App\Controllers\Api\ApiBaseController;
use App\Entities\User;
use \Firebase\JWT\JWT;

class App extends ApiBaseController
{
	public function index()
	{
		$user = new User(4);
		echo '<pre>';
		print_r($user->getRoles());
		echo '</pre>';
	}

	public function show404()
	{
		return $this->failNotFound();
	}
}
