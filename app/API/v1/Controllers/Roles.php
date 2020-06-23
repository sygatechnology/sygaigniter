<?php

namespace App\API\v1\Controllers;

/**
 * @package    App\Controller\Api
 * @author     SygaTechnology Dev Team
 * @copyright  2019 SygaTechnology Foundation
 */

use \App\Models\RoleModel;
use \App\Entities\Role;
use \Plugin\cms\Services\ObjectTypesService;

/**
 * Class Users
 *
 * @todo Users Resource Controller
 *
 * @package App\Controller\Api
 * @return CodeIgniter\RESTful\ResourceController
 */

use App\Controllers\BaseController;

class Roles extends BaseController
{
    public function index()
    {
        if ($this->currentUser->isAuthorized("list_roles")) {
            $params = \Config\Services::apiRequest();
            $limitVar = $params->getParam('limit');
            $offsetVar = $params->getParam('page');
            $order = $params->getParam('order');
            $orderSens = $params->getParam('order_sens');
            $withDeletedVar = $params->getParam('with_deleted');
            $deletedOnlyVar = $params->getParam('deleted_only');
            $limit = $limitVar !== null ? (int) $limitVar : 10;
            $offset = $offsetVar !== null ? (int) $offsetVar : 1;
            $withDeleted = $withDeletedVar != null ? true : false;
            $deletedOnly = $deletedOnlyVar != null ? true : false;
            $roleModel = new RoleModel();
            return $this->respond($roleModel->getResult($limit, $offset, $order, $orderSens, $withDeleted, $deletedOnly), 200);
        }
        return $this->failForbidden("List roles capability required");
    }

    public function create()
    {
        if ($this->currentUser->isAuthorized("create_role")) {
            $params = \Config\Services::apiRequest();
            $data = $params->params();
            $role = new Role();
            $role->fill($data);
            $roleModel = new RoleModel();
            if ($roleModel->insert($role) === false) {
                return $this->respond([$roleModel->errors()], 500);
            }
            return $this->respondCreated(['id' => $roleModel->getInsertID()]);
        }
        return $this->failForbidden("Create role capability required");
    }

    public function show($id = null)
    {
        if ($this->currentUser->isAuthorized("list_roles")) {
            $roleModel = new RoleModel();
            $role = $roleModel->find($id);
            if ($role) {
                return $this->respond($role->getResult(), 200);
            }
            return $this->respond((object) array(), 404);
        }
        return $this->failForbidden("List roles capability required");
    }

    public function update($id = null)
    {
        if ($this->currentUser->isAuthorized("update_role")) {
            $params = \Config\Services::apiRequest();
            $data = $params->params();
            if ($id !== null) {
                $role = new Role();
                $role->fill($data);
                $roleModel = new RoleModel();
                if ($roleModel->update($id, $role) === false) {
                    return $this->respond([$roleModel->errors()], 500);
                }
                return $this->respond(['Role updated'], 200);
            }
            return $this->respond(['Error on request'], 500);
        }
        return $this->failForbidden("Update role capability required");
    }

    public function delete($id = null)
    {
        if ($this->currentUser->isAuthorized("delete_role")) {
            if ($id !== null && is_numeric($id)) {
                $roleModel = new RoleModel();
                $role = $roleModel->find($id);
                if (!$role) return $this->failNotFound();
                $roleModel->delete($id);
                return $this->respondDeleted(['id' => $id]);
            }
            if ($id !== null && is_string($id) && $id == 'purge') {
                $roleModel = new RoleModel();
                $roleModel->purgeDeleted();
                return $this->respondDeleted(['Deleted roles purged']);
            }
            return $this->respond(['Error on request'], 500);
        }
        return $this->failForbidden("Delete role capability required");
    }
}
