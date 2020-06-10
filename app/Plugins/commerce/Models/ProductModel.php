<?php

namespace Plugin\commerce\Models;

/**
 * @package    Plugin\cms\Models
 * @author     SygaTechnology Dev Team
 * @copyright  2019 SygaTechnology Foundation
 */

use \App\Core\SY_Model;
use \Plugin\cms\Entities\Term;

/**
 * Class PostModel
 *
 * @todo Posts Resource Model
 *
 * @package Plugin\cms\Models
 */

class PostModel extends SY_Model
{
    protected $table                = 'posts';
    protected $primaryKey           = 'post_id';

    protected $returnType           = '\Plugin\commerce\Entities\Product';
    protected $useSoftDeletes       = true;

    protected $allowedFields        = [
        'post_id',
        'post_title',
        'post_name',
        'post_author',
        'post_content',
        'post_excerpt',
        'post_status',
        'comment_status',
        'post_parent',
        'post_type'
    ];

    protected $useTimestamps        = true;
    protected $createdField         = 'post_date';
    protected $updatedField         = 'post_modified';
    protected $deletedField         = 'post_deleted';

    protected $skipValidation       = false;
    protected $validationRules      = [
        'post_type'       => 'required'
    ];

    public function getResult($terms, $status = '*', $limit = 0, $numPage = 1, $order = '', $order_sens = '', $with_deleted = false, $only_deleted = true)
    {
        if ($only_deleted){
            $this->onlyDeleted();
        }
        if (! empty($status) && $status !== '*') $this->where('post_status', $status);
        if (! empty($order) && ! empty($order_sens)) $this->orderBy($order, $order_sens);
        $dbResult = ($limit > 0 && $numPage > 0) ? $this->findAll($limit, (((int)$numPage-1)*$limit)) : $this->findAll();
        $rows = [];
        foreach ($dbResult as $product) {
            $uResult = $product->getResult();
            /*$uResult['categories'] = $product->getTaxonomies('category');
            $uResult['tags'] = $product->getTaxonomies('tag');*/
            $rows[] = $uResult;
            unset($uResult);
        }
        $apiResult = \Config\Services::ApiResult();
        $where = (! empty($status) && $status !== '*') ? ['post_status =' => $status] : [];
        return $apiResult->set($rows, $this->countAllCompiledResults($with_deleted, $where), $limit, $numPage);
    }

    /**
     * Inserts data into the current table. If an object is provided,
     * it will attempt to convert it to an array.
     *
     * @param array|object $data
     * @param boolean      $returnID Whether insert ID should be returned or not.
     *
     * @return integer|string|boolean
     * @throws \ReflectionException
     */
    public function insert($data = NULL, bool $returnID = true){
        if (empty($data))
        {
          $data           = $this->tempData['data'] ?? null;
          $escape         = $this->tempData['escape'] ?? null;
          $this->tempData = [];
        }

        if (empty($data))
        {
          throw DataException::forEmptyDataset('insert');
        }

        if(! $data instanceof \Plugin\cms\Entities\Post ){
            throw new \InvalidArgumentException('data must be an instance of \Plugin\cms\Entities\Post.');
        }

        $productID = $this->insertPost($data->post_args);
        if( $productID ){
            helper('functions');
            $productmeta_args = [];
            foreach ($data->postmeta_args as $key => $value) {
                $productmeta_args[] = [
                    $this->primaryKey => $productID,
                    'meta_key' => $key,
                    'meta_value' => is_array($value) ? serialize_args($value) : $value
                ];
            }
            if(! empty($productmeta_args) ){
                if(! $this->updatePostMeta($productID, $productmeta_args)){
                    return false;
                }
            }

            $product_term_args = [];
            foreach ($data->post_term_args as $termId) {

                $product_term_args[] = [
                    'object_id' => $productID,
                    'term_taxonomy_id' => (new Term($termId))->getField( 'term_taxonomy_id' )
                ];
            }
            if(! empty($product_term_args) ){
                if(! $this->updatePostTerms($productID, $product_term_args)){
                    return false;
                }
            }
            return $productID;
        }
        return false;
    }

    private function insertPost($data){

        $escape = null;

        $this->insertID = 0;

        // If $data is using a custom class with public or protected
        // properties representing the table elements, we need to grab
        // them as an array.
        if (is_object($data) && ! $data instanceof stdClass)
        {
          $data = static::classToArray($data, $this->primaryKey, $this->dateFormat, false);
        }

        // If it's still a stdClass, go ahead and convert to
        // an array so doProtectFields and other model methods
        // don't have to do special checks.
        if (is_object($data))
        {
          $data = (array) $data;
        }

        // Validate data before saving.
        if ($this->skipValidation === false)
        {
          if ($this->validate($data) === false)
          {
            return false;
          }
        }

        // Must be called first so we don't
        // strip out created_at values.
        $data = $this->doProtectFields($data);

        // Set created_at and updated_at with same time
        $date = $this->setDate();

        if ($this->useTimestamps && ! empty($this->createdField) && ! array_key_exists($this->createdField, $data))
        {
          $data[$this->createdField] = $date;
        }

        if ($this->useTimestamps && ! empty($this->updatedField) && ! array_key_exists($this->updatedField, $data))
        {
          $data[$this->updatedField] = $date;
        }

        $data = $this->trigger('beforeInsert', ['data' => $data]);

        // Must use the set() method to ensure objects get converted to arrays
        $result = $this->builder()
            ->set($data['data'], '', $escape)
            ->insert();

        // If insertion succeeded then save the insert ID
        if ($result)
        {
            $this->insertID = $this->db->insertID();
        }

        // Trigger afterInsert events with the inserted data and new ID
        $this->trigger('afterInsert', ['id' => $this->insertID, 'data' => $data, 'result' => $result]);

        // If insertion failed, get out of here
        if (! $result)
        {
          return $result;
        }

        // return the insertID.
        return $this->insertID;
    }

    /**
  	 * Updates a single record in $this->table. If an object is provided,
  	 * it will attempt to convert it into an array.
  	 *
  	 * @param integer|array|string $id
  	 * @param array|object         $data
  	 *
  	 * @return boolean
  	 * @throws \ReflectionException
  	 */
  	public function update($id = null, $data = null): bool
  	{
      if (is_numeric($id) || is_string($id))
  		{
  			$id = [$id];
  		}

  		if (empty($data))
  		{
  			$data           = $this->tempData['data'] ?? null;
  			$escape         = $this->tempData['escape'] ?? null;
  			$this->tempData = [];
  		}

  		if (empty($id) || empty($data))
  		{
  			throw DataException::forEmptyDataset('update');
  		}

      if(! $data instanceof \Plugin\cms\Entities\Post ){
          throw new \InvalidArgumentException('data must be an instance of \Plugin\cms\Entities\Post.');
      }

      $result = $this->updatePost($id, $data->post_args);
      if( $result ){
          helper('functions');
          $productmeta_args = [];
          foreach ($data->postmeta_args as $key => $value) {
              $productmeta_args[] = [
                  $this->primaryKey => $id,
                  'meta_key' => $key,
                  'meta_value' => is_array($value) ? serialize_args($value) : $value
              ];
          }
          if(! empty($productmeta_args) ){
              $rows = $this->updatePostMeta($id, $productmeta_args);
              if(! $rows){
                  return false;
              }
          }

          $product_term_args = [];
          foreach ($data->post_term_args as $termId) {
              $product_term_args[] = [
                  'object_id' => $id,
                  'term_taxonomy_id' => (new Term($termId))->getField( 'term_taxonomy_id' )
              ];
          }
          if(! empty($product_term_args) ){
              $rows = $this->updatePostTerms($id, $product_term_args);
              if(! $rows){
                  return false;
              }
          }
          return true;
      }
      return false;
  	}

    private function updatePost($id, $data){

        $escape = null;

        // If $data is using a custom class with public or protected
        // properties representing the table elements, we need to grab
        // them as an array.
        if (is_object($data) && ! $data instanceof stdClass)
        {
          $data = static::classToArray($data, $this->primaryKey, $this->dateFormat);
        }

        // If it's still a stdClass, go ahead and convert to
        // an array so doProtectFields and other model methods
        // don't have to do special checks.
        if (is_object($data))
        {
          $data = (array) $data;
        }

        // Validate data before saving.
        if ($this->skipValidation === false)
        {
          if ($this->validate($data) === false)
          {
            return false;
          }
        }

        // Must be called first so we don't
        // strip out updated_at values.
        $data = $this->doProtectFields($data);

        if ($this->useTimestamps && ! empty($this->updatedField) && ! array_key_exists($this->updatedField, $data))
        {
          $data[$this->updatedField] = $this->setDate();
        }

        $data = $this->trigger('beforeUpdate', ['id' => $id, 'data' => $data]);

        $builder = $this->builder();

        if ($id)
        {
          $builder = $builder->whereIn($this->table . '.' . $this->primaryKey, $id);
        }

        // Must use the set() method to ensure objects get converted to arrays
        $result = $builder
            ->set($data['data'], '', $escape)
            ->update();

        $this->trigger('afterUpdate', ['id' => $id, 'data' => $data, 'result' => $result]);

        return $result;
    }

    private function updatePostMeta($productID, $data){
        // If it's still a stdClass, go ahead and convert to
        // an array so doProtectFields and other model methods
        // don't have to do special checks.
        if (is_object($data))
        {
          $data = (array) $data;
        }

        $data = $this->trigger('beforeInsert', ['data' => $data]);

        $deleteResult = $this->deletePostMeta($productID);

        if(! $deleteResult){
            return $deleteResult;
        }

        $result = $db->table('postmeta')
                ->insertBatch($data['data']);

        // Trigger afterInsert events with the inserted data and number of rows
        $this->trigger('afterInsert', ['row' => $result, 'data' => $data, 'result' => $result]);

        // return the result.
        return $result;
    }

    private function updatePostTerms($productID, $data){
        // If it's still a stdClass, go ahead and convert to
        // an array so doProtectFields and other model methods
        // don't have to do special checks.
        if (is_object($data))
        {
          $data = (array) $data;
        }

        $data = $this->trigger('beforeInsert', ['data' => $data]);

        $deleteResult = $this->deletePostTerms($productID);

        if(! $deleteResult){
            return $deleteResult;
        }

        $db      = \Config\Database::connect();
        $result = $db->table('term_relationships')
                ->insertBatch($data['data']);

        // Trigger afterInsert events with the inserted data and number of rows
        $this->trigger('afterInsert', ['row' => $result, 'data' => $data, 'result' => $result]);

        // return the result.
        return $result;
    }

    public function getDeleted(){
        $builder = $this->builder();
        return $builder->where($this->deletedField.' <>', '0000-00-00 00:00:00')->get()->getResult();
    }

    public function deleteTermRelationships($posID){
        $db      = \Config\Database::connect();
        return $db->table('term_relationships')
                    ->where('object_id', $posID)
                    ->delete();
    }

    public function deletePostMeta($productID){
        $db      = \Config\Database::connect();
        return $db->table('postmeta')
                    ->where($this->primaryKey, $productID)
                    ->delete();
    }

    public function deletePostTerms($productID){
        $db      = \Config\Database::connect();
        return $db->table('term_relationships')
                      ->where('object_id', $productID)
                      ->delete();
    }

    public function purgeTermRelationships(array $product_ids){
        $db      = \Config\Database::connect();
        return $db->table('term_relationships')
                    ->whereIn('object_id', $product_ids)
                    ->delete();
    }

    public function purgePostMeta(array $product_ids){
        if(! empty($product_ids)){
            $db      = \Config\Database::connect();
            $db->table('postmeta')
                      ->whereIn($this->primaryKey, $product_ids)
                      ->delete();
        }
    }

    public function purgePostTerms(array $product_ids){
        if(! empty($product_ids)){
            $db      = \Config\Database::connect();
            $db->table('term_relationships')
                      ->whereIn('object_id', $product_ids)
                      ->delete();
        }
    }

    public function purgePosts(array $product_ids){
        $builder = $this->builder();
        $builder->whereIn($this->primaryKey, $product_ids)->delete();
    }
}
