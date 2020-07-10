<?php

namespace App\API\v1\Controllers;

/**
 * @package    App\Controller\Api
 * @author     SygaTechnology Dev Team
 * @copyright  2019 SygaTechnology Foundation
 */

use \App\Models\CapabilityModel;
use \App\Entities\Capability;

/**
 * Class Users
 *
 * @todo Users Resource Controller
 *
 * @package App\Controller\Api
 * @return CodeIgniter\RESTful\ResourceController
 */

use App\Controllers\BaseController;

class Capabilities extends BaseController
{
    public function index()
    {
        if ($this->currentUser->isAuthorized("list_capabilities")) {
            $params = \Config\Services::apiRequest();
            $limitVar = $params->getParam('limit');
            $offsetVar = $params->getParam('page');
            $order = $params->getParam('order');
            $orderSens = $params->getParam('order_sens');
            $withDeletedVar = $params->getParam('with_deleted');
            $deletedOnlyVar = $params->getParam('deleted_only');
            $limit = !is_null($limitVar) ? (int) $limitVar : 10;
            $offset = !is_null($offsetVar) ? (int) $offsetVar : 1;
            $withDeleted = !is_null($withDeletedVar) ? true : false;
            $deletedOnly = !is_null($deletedOnlyVar) ? true : false;
            $capabilityModel = new CapabilityModel();
            $capabilityModel
                ->setLimit($limit, $offset)
                ->setOrder($order, $orderSens)
                ->paginateResult();
            if($deletedOnly) $capabilityModel->onlyDelete();
            if($withDeleted) $capabilityModel->withDeleted();
            return $this->respond($capabilityModel->getResult(), 200);
        }
        return $this->failForbidden("List capabilities capability required");
    }

    public function create()
    {
        if ($this->currentUser->isAuthorized("create_capability")) {
            $params = \Config\Services::apiRequest();
            $data = $params->params();
            $capability = new Capability();
            $capability->fill($data);
            $capabilityModel = new CapabilityModel();
            if ($capabilityModel->insert($capability) === false) {
                return $this->respond([$capabilityModel->errors()], 500);
            }
            return $this->respondCreated(['id' => $capabilityModel->getInsertID()]);
        }
        return $this->failForbidden("Create capability capability required");
    }

    public function show($id = null)
    {
        if ($this->currentUser->isAuthorized("list_capabilities")) {
            $capabilityModel = new CapabilityModel();
            $capability = $capabilityModel->find($id);
            if ($capability) {
                return $this->respond($capability->getResult(), 200);
            }
            return $this->failNotFound();
        }
        return $this->failForbidden("List capabilities capability required");
    }

    public function update($id = null)
    {
        if ($this->currentUser->isAuthorized("update_capability")) {
            $params = \Config\Services::apiRequest();
            $data = $params->params();
            if ($id !== null) {
                $capability = new Capability();
                $capability->fill($data);
                $capabilityModel = new CapabilityModel();
                if ($capabilityModel->update($id, $capability) === false) {
                    return $this->respond([$capabilityModel->errors()], 500);
                }
                return $this->respond(['Role updated'], 200);
            }
            return $this->respond(['Error on request'], 500);
        }
        return $this->failForbidden("Update capability capability required");
    }

    public function delete($id = null)
    {
        if ($this->currentUser->isAuthorized("delete_capability")) {
            if ($id !== null && is_numeric($id)) {
                $capabilityModel = new CapabilityModel();
                $data['capability_id'] = $id;
                $capability = $capabilityModel->find($id);
                //if( $capability->isNull() ) return $this->failNotFound();
                $capabilityModel->deleteRoleCapabilities($id);
                $capabilityModel->delete($id);
                return $this->respondDeleted(['id' => $id]);
            }
            if ($id !== null && is_string($id) && $id == 'purge') {
                $capabilityModel = new CapabilityModel();
                $capabilityModel->purgeDeleted();
                return $this->respondDeleted(['Deleted capabilities purged']);
            }
            return $this->respond(['Error on request'], 500);
        }
        return $this->failForbidden("Delete capability capability required");
    }
}
