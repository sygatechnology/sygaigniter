<?php namespace App\API\v1\Services;

use CodeIgniter\HTTP\RequestInterface;

class CurrentUser
{
	public function isAuthorized(string $capability): bool
	{
		$auth = \Config\Services::auth();
		if($auth->isLoggedIn()){
			$session = \Config\Services::session();
			$roleSlugs = [];
			foreach ($session->get('roles') as $slug => $label) {
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

    public function getId(){
        $session = \Config\Services::session();
        return (int) $session->get('id');
    }
}
