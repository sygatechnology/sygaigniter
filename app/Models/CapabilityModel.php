<?php namespace App\Models;

/**
 * @package    App\Models
 * @author     SygaTechnology Dev Team
 * @copyright  2019 SygaTechnology Foundation
 */

use \App\Core\SY_Model;

/**
 * Class UserModel
 *
 * @todo Users Resource Model
 *
 * @package App\Models
 */

class CapabilityModel extends SY_Model
{
    protected $table = 'capabilities';
    protected $primaryKey = 'capability_id';

    protected $returnType = 'App\Entities\Capability';
    protected $useSoftDeletes = true;

    protected $allowedFields = ['slug', 'label'];

    protected $useTimestamps = false;
    protected $deletedField = 'deleted_at';

    protected $skipValidation = false;
    protected $validationRules = [
        'slug' => 'required|is_unique[capabilities.slug]|alpha_dash|min_length[4]',
        'label' => 'required|min_length[4]',
    ];

    public function getResult(int $limit = null, int $numPage = null, string $order = null, string $order_sens = null, $with_deleted = false, $only_deleted = true)
    {
        if ($only_deleted) {
            $this->onlyDeleted();
        } else {
            if (!$with_deleted && !$only_deleted) {
                $this->withoutDeleted();
            }
        }
        if ($order !== null && $order_sens !== null) {
            $this->order_by($order, $order_sens);
        }

        $dbResult = ($limit !== null && $numPage !== null && (int) $numPage > -1) ? $this->findAll($limit, $numPage) : $this->findAll();
        $rows = [];
        foreach ($dbResult as $role) {
            $rows[] = $role->getResult();
        }
        $apiResult = \Config\Services::ApiResult();
        return $apiResult->set($rows, $this->countAllCompiledResults($with_deleted), $limit, $numPage);
    }

}
