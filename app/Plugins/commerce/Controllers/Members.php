<?php

namespace App\Plugins\commerce\Controllers;

/**
 * @package    Plugin\commerce\Controllers
 * @author     SygaTechnology Dev Team
 * @copyright  2019 SygaTechnology Foundation
 */
use Plugin\commerce\Models\MemberModel;
use Plugin\cms\Entities\Term;

/**
 * Class Terms
 *
 * @todo Terms Resource Controller
 *
 * @package App\Controller\Api
 * @return CodeIgniter\RESTful\ResourceController
 */

use App\Controllers\BaseController;
use App\Entities\User;
use \App\Models\RoleModel;
use App\Models\UserModel;
use Plugin\commerce\Entities\Member;

class Members extends BaseController
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
            $account_status = ($statusVar !== null) ? (($statusVar === 'all') ? null : $statusVar) : null;
            $limit = $limitVar !== null ? (int) $limitVar : 10;
            $offset = $offsetVar !== null ? (int) $offsetVar : 1;
            $withDeleted = $withDeletedVar != null ? true : false;
            $deletedOnly = $deletedOnlyVar != null ? true : false;
            $memberModel = new MemberModel();
            return $this->respond($memberModel->getResult($account_status, $limit, $offset, $order, $orderSens, $withDeleted, $deletedOnly), 200);
        }
        return $this->failForbidden("List users capability required");
    }

    public function create()
    {
        if ($this->currentUser->isAuthorized("create_user")) {
            $params = \Config\Services::apiRequest();
            $data = $params->params();
            $data['role'] = "sc_member";
            $data['username'] = $data['login'];
            $data['lastname'] = $data['login'];
            $user = new User();
            $user->fill($data);
            $userModel = new UserModel();
            if ($userModel->insert($user) === false) {
                return $this->respond([$userModel->errors()], 400);
            }
            $user_id = $userModel->getInsertID();
            $data['user_id'] = $user_id;
            $member = new Member();
            $member->fill($data);
            $memberModel = new MemberModel();
            if ($memberModel->insert($member) === false) {
                return $this->respond([$memberModel->errors()], 400);
            }
            $userModel->insertRole($user_id, $data['role']);
            return $this->respondCreated(['id' => $memberModel->getInsertID()]);
        }
        return $this->failForbidden("Create user capability required");
    }

    public function show($id = null)
    {
        if ($this->currentUser->isAuthorized("list_users")) {
            $memberModel = new MemberModel();
            $user = $memberModel->find($id);
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
                $memberModel = new MemberModel();
                if ($memberModel->update($id, $user) === false) {
                    return $this->respond([$memberModel->errors()], 500);
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
                $memberModel = new MemberModel();
                $user = $memberModel->find($id);
                if (!$user) return $this->failNotFound();
                $memberModel->delete($id);
                return $this->respondDeleted(['id' => $id]);
            }
            if ($id !== null && is_string($id) && $id == 'purge') {
                $memberModel = new MemberModel();
                $memberModel->purgeDeleted();
                return $this->respondDeleted(['Deleted users purged']);
            }
            return $this->respond(['Error on request'], 500);
        }
        return $this->failForbidden("Delete user capability required");
    }
}
