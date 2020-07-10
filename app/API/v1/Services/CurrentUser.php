<?php namespace App\API\v1\Services;

use App\Entities\User;
use CodeIgniter\HTTP\RequestInterface;

class CurrentUser
{
	public function isAuthorized(string $capability): bool
	{
		$auth = \Config\Services::auth();
		if($auth->isLoggedIn()){
			$request = \Config\Services::apiRequest();
			$token = $request->getToken();
			$jwtService = \Config\Services::JWT();
			$payload = (object) $jwtService::decode($token);
			$roleSlugs = [];
			$user = new User(['user_id' => $payload->user->id]);
			$roles = $user->getRoles();
			if(count($roles)>0){
				foreach ($roles as $slug => $label) {
					$roleSlugs[] = $slug;
				}
				$db = \Config\Database::connect();
				$rows = $db->table('role_capabilities')
											->whereIn('role_slug', $roleSlugs)
											->where('capability_slug', 'have_all_capabilities')
											->orWhere('capability_slug', $capability)
											->get()->getResult();
				unset($roleSlugs);
				return (count($rows) > 0) ? true : false;
			}
			return false;
		}
		return false;
    }

    public function getId(){
        $request = \Config\Services::apiRequest();
		$token = $request->getToken();
		$jwtService = \Config\Services::JWT();
		$payload = (object) $jwtService::decode($token);
		return $payload->user->id;
    }
}
