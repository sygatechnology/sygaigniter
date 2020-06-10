<?php

namespace App\Plugins\commerce\Controllers;

/**
 * @package    Plugin\commerce\Controllers
 * @author     SygaTechnology Dev Team
 * @copyright  2019 SygaTechnology Foundation
 */
use Plugin\cms\Models\TaxonomyModel;
use Plugin\commerce\Models\ProductModel;
use Plugin\cms\Entities\Post;

/**
 * Class Posts
 *
 * @todo Posts Resource Controller
 *
 * @package App\Controller\Api
 * @return CodeIgniter\RESTful\ResourceController
 */

use App\Controllers\BaseController;
use \Plugin\cms\Services\ObjectTypesService;
use \Plugin\cms\Args\PostArgs;

class Products extends BaseController
{
    public function index()
    {
        $params = \Config\Services::apiRequest();
        $termVar = $params->getParam('term');
        $statusVar = $params->getParam('status');
        $limitVar = $params->getParam('limit');
        $offsetVar = $params->getParam('page');
        $order = $params->getParam('order');
        $orderSens = $params->getParam('order_sens');
        $withDeletedVar = $params->getParam('with_deleted');
        $deletedOnlyVar = $params->getParam('deleted_only');
        $terms = []; 
        if( !is_null($termVar) ) {
            $termVar = trim($termVar);
            if( starts_with($termVar, '[') && ends_with($termVar, ']')) {
                $terms = json_decode($termVar); 
            } else {
                $terms = [$termVar]; 
            }
        }
        $status = is_null($statusVar) ? '*' : $statusVar;
        $limit = $limitVar !== null ? (int) $limitVar : 10;
        $offset = $offsetVar !== null ? (int) $offsetVar : 1;
        $withDeleted = $withDeletedVar != null ? true : false;
        $deletedOnly = $deletedOnlyVar != null ? true : false;
        $productModel = new ProductModel();
        return $this->respond($productModel->getResult($terms, $status, $limit, $offset, $order, $orderSens, $withDeleted, $deletedOnly), 200);
    }

    public function show($id = null)
    {
        $productModel = new ProductModel();
        $product = $productModel->find($id);
        if ($product) {
            return $this->respond($product->getResult(), 200);
        }
        return $this->respond((object) array(), 404);
    }

    public function create()
    {
        if ($this->currentUser->isAuthorized("edit_product")) {

            $productArgsObject = $this->setArgs();

            if(! $productArgsObject->isValidPost()){
                return $this->respond($productArgsObject->errors(), 500);
            }

            // Post Args
            $productArgs = $productArgsObject->getArgs();

            // Post Meta Args
            $productMetaArgs = $productArgsObject->getMetaArgs();

            // Post Terms Args
            $productTermArgs = $productArgsObject->getTermArgs();

            $product = new Post();

            unset($productArgs['post_id']);

            $productData = [
                'post_args' => $productArgs,
                'postmeta_args' => $productMetaArgs,
                'post_term_args' => $productTermArgs
            ];
            $product->fillArgs($productData);

            $productModel = new PostModel();

            if ($productModel->insert($product) === false) {
                return $this->respond([$productModel->errors()], 400);
            }
            return $this->respondCreated(['id' => $productModel->getInsertID()]);
        }
        return $this->failForbidden("Create post capability required");
    }

    public function update($id = null)
    {
        if ($this->currentUser->isAuthorized("edit_product")) {

            $productArgsObject = $this->setArgs($id);

            if(! $productArgsObject->isValidPost()){
                return $this->respond($productArgsObject->errors(), 500);
            }

            // Post Args
            $productArgs = $productArgsObject->getArgs();

            // Post Meta Args
            $productMetaArgs = $productArgsObject->getMetaArgs();

            // Post Terms Args
            $productTermArgs = $productArgsObject->getTermArgs();

            $product = new Post();

            $productArgs['post_id'] = $productArgsObject->getID();

            $productData = [
                'post_args' => $productArgs,
                'postmeta_args' => $productMetaArgs,
                'post_term_args' => $productTermArgs
            ];
            $product->fillArgs($productData);

            $productModel = new PostModel();

            if ($productModel->update($productArgsObject->getID(), $product) === false) {
                return $this->respond([$productModel->errors()], 400);
            }
            return $this->respond(['Post updated'], 200);
        }
        return $this->failForbidden("Update post capability required");
    }

    private function setArgs($id = null){
        $request = \Config\Services::apiRequest();
        $productArgsObject = new PostArgs();
        $productArgsObject->fill($request, $id);
        return $productArgsObject;
    }

    public function delete($id = null)
    {
        if ($this->currentUser->isAuthorized("delete_post")) {
            if ($id !== null && is_numeric($id)) {
                $productModel = new PostModel();
                $product = new Post($id);
                if ($product->isNull()) return $this->failNotFound();
                $termModel->deleteTermRelationships($id);
                $productModel->deletePostMeta($id);
                $productModel->deletePostTerms($id);
                $productModel->delete($id);
                return $this->respondDeleted(['id' => $id]);
            }
            if ($id !== null && is_string($id) && $id == 'purge') {
                $productModel = new PostModel();
                $products = $productModel->getDeleted();
                $ids = [];
                foreach($products as $product){
                    $ids[] = $product->post_id;
                }
                $productModel->purgeTermRelationships($ids);
                $productModel->purgePostMeta($ids);
                $productModel->purgePostTerms($ids);
                $productModel->purgePosts($ids);
                return $this->respondDeleted(['Deleted posts purged']);
            }
            return $this->respond(['Error on request'], 500);
        }
        return $this->failForbidden("Delete post capability required");
    }
}
