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

class Users extends ApiBaseController
{
    public function index()
    {
        if ($this->currentUser->isAuthorized("list_users")) {
            $params = \Config\Services::apiRequest();
            $statusVar = $params->getParam('status');
            $limitVar = $params->getParam('limit');
            $offsetVar = $params->getParam('page');
            $order = $params->getParam('order');
            $orderSens = $params->getParam('order_sens');
            $withDeletedVar = $params->getParam('with_deleted');
            $deletedOnlyVar = $params->getParam('deleted_only');
            $status = ($statusVar !== null) ? (($statusVar === 'all') ? null : $statusVar) : null;
            $limit = $limitVar !== null ? (int) $limitVar : 10;
            $offset = $offsetVar !== null ? (int) $offsetVar : 1;
            $withDeleted = $withDeletedVar != null ? true : false;
            $deletedOnly = $deletedOnlyVar != null ? true : false;
            $userModel = new UserModel();
            $userModel
                ->setStatus($status)
                ->setLimit($limit, $offset)
                ->setOrder($order, $orderSens)
                ->paginateResult();
            if($deletedOnly) $userModel->onlyDelete();
            if($withDeleted) $userModel->withDeleted();
            $result = $userModel->formatResult();
            $db = \Config\Database::connect();
            $data = [];
            foreach($result['data'] as $user){
                $user->roles = $user->getRoles();
                $data[] = $user;
            }
            $result['data'] = $data;
            return $this->respond($result, 200);
        }
        return $this->failForbidden("List users capability required");
    }

    public function create()
    {
        if ($this->currentUser->isAuthorized("create_user")) {
            $params = \Config\Services::apiRequest();
            $data = $params->params();
            $roleModel = new RoleModel();
            if (!isset($data['role'])) {
                $data['role'] = $roleModel->getDefault();
                if ($data['role'] == null) {
                    return $this->fail('API does not have a default user role');
                }
            } else {
                if (!$roleModel->exists($data['role'])) {
                    return $this->fail('User role ' . $data['role'] . ' does not exists');
                }
            }
            $user = new User();
            $user->fill($data);
            $userModel = new UserModel();
            if ($userModel->insert($user) === false) {
                return $this->respond([$userModel->errors()], 400);
            }
            $user_id = $userModel->getInsertID();

            $userModel->insertRole($user_id, $data['role']);
            return $this->respondCreated(['id' => $user_id]);
        }
        return $this->failForbidden("Create user capability required");
    }

    public function show($id = null)
    {
        if ($this->currentUser->isAuthorized("list_users")) {
            $userModel = new UserModel();
            $user = $userModel->find($id);
            if ($user) {
                return $this->respond($user->getResult(), 200);
            }
            return $this->respond((object) array(), 404);
        }
        return $this->failForbidden("List users capability required");
    }

    public function update($id = null)
    {
        if ($this->currentUser->isAuthorized("update_user")) {
            $params = \Config\Services::apiRequest();
            $data = $params->params();
            if ($id !== null) {
                $user = new User();
                $user->fill($data);
                $userModel = new UserModel();
                if ($userModel->update($id, $user) === false) {
                    return $this->respond([$userModel->errors()], 500);
                }
                return $this->respond(['User updated'], 200);
            }
            return $this->respond(['Error on request'], 500);
        }
        return $this->failForbidden("Update user capability required");
    }

    public function delete($id = null)
    {
        if ($this->currentUser->isAuthorized("delete_user")) {
            if ($id !== null && is_numeric($id)) {
                $userModel = new UserModel();
                $user = $userModel->find($id);
                if (!$user) return $this->failNotFound();
                $userModel->delete($id);
                return $this->respondDeleted(['id' => $id]);
            }
            if ($id !== null && is_string($id) && $id == 'purge') {
                $userModel = new UserModel();
                $userModel->purgeDeleted();
                return $this->respondDeleted(['Deleted users purged']);
            }
            return $this->respond(['Error on request'], 500);
        }
        return $this->failForbidden("Delete user capability required");
    }
}
