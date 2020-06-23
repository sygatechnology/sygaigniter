<?php

namespace App\API\v1\Controllers;

/**
 * @package    App\Controller\Api
 * @author     SygaTechnology Dev Team
 * @copyright  2019 SygaTechnology Foundation
 */

use \App\Models\UserModel;
use \App\Models\RoleModel;
use \App\Entities\User;

/**
 * Class Users
 *
 * @todo Users Resource Controller
 *
 * @package App\Controller\Api
 * @return CodeIgniter\RESTful\ResourceController
 */

use App\Controllers\BaseController;

class Profile extends BaseController
{
    public function index()
    {
        $userModel = new UserModel();
        $user = $userModel->find(\Config\Services::currentUser()->getId());
        return $this->respond($user->getResult(), 200);
    }

    public function up()
    {
        $params = \Config\Services::apiRequest();
        $data = $params->params();
        $user = new User();
        $user->fill($data);
        $userModel = new UserModel();
        if ($userModel->update(\Config\Services::currentUser()->getId(), $user) === false) {
            return $this->respond([$userModel->errors()], 500);
        }
        return $this->respond(['Profile updated'], 200);
    }
}
