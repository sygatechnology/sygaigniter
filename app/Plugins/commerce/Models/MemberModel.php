<?php

namespace Plugin\commerce\Models;

/**
 * @package    Plugin\commerce\Models
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

class MemberModel extends SY_Model
{
    protected $table                = 'sc_users';
    protected $primaryKey           = 'id';

    protected $returnType           = '\Plugin\commerce\Entities\Member';
    protected $useSoftDeletes       = true;

    //protected $allowedFields        = ['uname', 'fname', 'lname', 'email', 'psswd', 'status', 'reset_psswd_token', 'reset_psswd_validity'];

    protected $useTimestamps        = true;
    protected $createdField         = 'created_at';
    protected $updatedField         = 'updated_at';
    protected $deletedField         = 'deleted_at';

    protected $skipValidation       = false;
    protected $validationRules      = [
        'login'       => 'required|alpha_numeric_space|min_length[3]|is_unique[sc_users.login]',
        'email'       => 'required|valid_email|is_unique[sc_users.email]'
    ];

    public function getResult(string $status = null, int $limit = null, int $numPage = null, string $order = null, string $order_sens = null, $with_deleted = false, $only_deleted = true)
    {
        if ($only_deleted){
            $this->onlyDeleted();
        } else {
            if (!$with_deleted && !$only_deleted) $this->withoutDeleted();
        }
        if ($status !== null) $this->where('status', $status);
        if ($order !== null && $order_sens !== null) $this->orderBy($order, $order_sens);
        if ($numPage === null) $numPage = 1;
        $dbResult = ($limit !== null && $numPage !== null && $numPage > -1) ? $this->findAll($limit, (((int)$numPage-1)*$limit)) : $this->findAll();
        $rows = [];
        foreach ($dbResult as $user) {
            $uResult = $user->getResult();
            $rows[] = $uResult;
            unset($uResult);
        }
        $apiResult = \Config\Services::ApiResult();
        $where = (!is_null($status)) ? ['status =' => $status] : [];
        return $apiResult->set($rows, $this->countAllCompiledResults($with_deleted, $where), $limit, $numPage);
    }
}
