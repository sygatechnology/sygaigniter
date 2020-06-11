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
    protected $allowedFields = [
        "id",
        "user_id",
        "login",
        "email",
        "real_name",
        "birthday",
        "gender",
        "item_count",
        "followers_count",
        "following_count",
        "positive_feedback_count",
        "neutral_feedback_count",
        "negative_feedback_count",
        "meeting_transaction_count",
        "feedback_reputation",
        "expose_location",
        "city_id",
        "created_at",
        "updated_at",
        "last_loged_on"
    ];

    protected $useTimestamps        = true;
    protected $createdField         = 'created_at';
    protected $updatedField         = 'updated_at';
    protected $deletedField         = 'deleted_at';

    protected $skipValidation       = false;
    protected $validationRules      = [
        'login'       => 'required|alpha_numeric_space|min_length[3]|is_unique[sc_users.login]',
        'email'       => 'required|valid_email|is_unique[sc_users.email]'
    ];

    public function getResult(string $account_status = null, int $limit = null, int $numPage = null, string $order = null, string $order_sens = null, $with_deleted = false, $only_deleted = true)
    {
        if ($only_deleted){
            $this->onlyDeleted();
        } else {
            if (!$with_deleted && !$only_deleted) $this->withoutDeleted();
        }
        if ($account_status !== null) $this->where('account_status', $account_status);
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
        $where = (!is_null($account_status)) ? ['account_status =' => $account_status] : [];
        return $apiResult->set($rows, $this->countAllCompiledResults($with_deleted, $where), $limit, $numPage);
    }
}
