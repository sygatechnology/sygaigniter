<?php
namespace App\Core;

use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Model;
use CodeIgniter\Validation\ValidationInterface;

class SY_Model extends Model
{

    protected $afterFind = [];

    /**
     * Model constructor.
     *
     * @param ConnectionInterface $db
     * @param ValidationInterface $validation
     */
    public function __construct(ConnectionInterface &$db = null, ValidationInterface $validation = null)
    {
        parent::__construct($db, $validation);
    }

    protected $paginateResult = false;
    protected $limit = 20;
    protected $numPage = 1;
    protected $offset = 0;

    public function setLimit(int $limit = null, $numPage = 1): SY_Model
    {
        if($limit !== null && $numPage !== null && $numPage > -1){
            $this->limit = $limit;
            $this->numPage = $numPage;
            $this->offset = ($numPage === 0 ? 0 : ((int)$numPage-1)*$limit);
        }
        return $this;
    }

    public function setOrder($orderBy, $orderSens): SY_Model
    {
        if($orderBy !== null && $orderSens !== null)
            $this->orderBy($orderBy, $orderSens);

        return $this;
    }

    public function setOrderSens(int $orderSens): SY_Model
    {
        $this->orderSens = $orderSens;
        return $this;
    }

    public function paginateResult(): SY_Model
    {
        $this->afterFind[] = 'setApiResult';
        $this->paginateResult = true;
        return $this;
    }

    protected function setApiResult(array $data)
    {
        $lastQuery = $this->getLastQuery()->getQuery();
        $segment = explode("from", strtolower($lastQuery));
        $sqlQuery = "select count(*) as count from" . $segment[1];
        $db = \Config\Database::connect();
        $result = $db->query($sqlQuery)->getResultArray();
        return [
            'data' => [
                'data' => $data['data'],
                'count' => (int) $result[0]['count']
            ]
        ];
    }

    public function getResult() {
        $result = $this->findAll($this->limit, $this->offset);
        $apiResult = \Config\Services::ApiResult();
        return !$this->paginateResult ? $result : $apiResult->set($result['data'], $result['count'], $this->limit, $this->numPage);
    }
}
