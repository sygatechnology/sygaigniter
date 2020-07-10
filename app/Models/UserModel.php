<?php

namespace App\Models;

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

class UserModel extends SY_Model
{
    protected $table                = 'users';
    protected $primaryKey           = 'user_id';

    protected $returnType           = 'App\Entities\User';
    protected $useSoftDeletes       = true;

    protected $allowedFields        = ['username', 'firstname', 'lastname', 'email', 'password', 'status', 'reset_psswd_token', 'reset_psswd_validity'];

    protected $useTimestamps        = true;
    protected $createdField         = 'created_at';
    protected $updatedField         = 'updated_at';
    protected $deletedField         = 'deleted_at';

    protected $skipValidation       = false;
    protected $validationRules      = [
        'password'       => 'required|min_length[8]',
        'username'       => 'required|alpha_numeric_space|min_length[3]|is_unique[users.username]',
        'lastname'       => 'min_length[3]',
        'email'          => 'required|valid_email|is_unique[users.email]'
    ];

    private $status = null;

    public function setStatus($status): UserModel
    {
        if ($status !== null) $this->where('status', $status);
        return $this;
    }

    public function insertRole($user_id, $role_slug)
    {
        $db = \Config\Database::connect();
        $data = array(
            'user_id' => $user_id,
            'role_slug'  => $role_slug
        );
        if ($db->table('user_roles')->insert($data)) {
            return $this;
        }
        return false;
    }
}
