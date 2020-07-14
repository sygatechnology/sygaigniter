<?php

namespace App\Controllers\Api;

/**
 * @package    App\Controllers\Api
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

use App\Controllers\Api\ApiBaseController;

class Profile extends ApiBaseController
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
