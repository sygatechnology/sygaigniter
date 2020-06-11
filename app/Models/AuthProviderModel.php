<?php

namespace App\Models;

/**
 * @package    App\Models
 * @author     SygaTechnology Dev Team
 * @copyright  2019 SygaTechnology Foundation
 */

//use \App\Core\SY_Model;
use CodeIgniter\Model;
use \App\Models\UserModel;
use \App\Entities\User;

/**
 * Class UserModel
 *
 * @todo Users Resource Model
 *
 * @package App\Models
 */

class AuthProviderModel extends Model
{
    protected $table                = 'users';
    protected $primaryKey           = 'user_id';

    protected $allowedFields        = ['current_token'];
    protected $returnType           = 'App\Entities\User';
    protected $useSoftDeletes       = true;

    protected $successed            = false;
    protected $error                = "";
    protected $errorCode            = null;
    protected $errorDescription     = "";
    protected $token                = "";
    protected $tokenExpiration      = 0;

    /**
     * @var \App\Entities\User $user The user
     */
    protected $user                 = null;

    public function __construct()
    {
        helper('user');
    }

    public function getResult(string $status = null, int $limit = null, int $offset = null, string $order = null, string $order_sens = null, int $deleted = null)
    {
        if ($status !== null) $this->where('status', $status);
        if ($deleted !== null) $this->where('deleted', (int) $deleted);
        if ($order !== null && $order_sens !== null) $this->order_by($order, $order_sens);
        $dbResult = ($limit !== null && $offset !== null) ? $this->findAll($limit, $offset) : $this->findAll();
        $result = [];
        foreach ($dbResult as $u) {
            $result[] = $u->getResult();
        }
        return $result;
    }

    public function authenticate($userName, $password)
    {
        if ($userName !== null && $password !== null) {
            $row = $this
                    ->groupStart()
                        ->where('uname', $userName)
                        ->orGroupStart()
                            ->where('email', $userName)
                        ->groupEnd()
                    ->groupEnd()
                    ->get()
                    ->getFirstRow();
            if ($row !== null && is_valid_password($password, $row->psswd)) {
                switch ($row->status) {
                    case 'pending':
                        $this->successed = false;
                        $this->error = "User acount not actived";
                        $this->errorCode = 401;
                        $this->errorDescription = "access_denied";
                        break;
                    case 'deleted':
                        $this->successed = false;
                        $this->error = "User acount not found";
                        $this->errorCode = 404;
                        $this->errorDescription = "resource_not_found";
                        break;
                    default:
                        $userModel = new UserModel();
                        $this->user = $userModel->find($row->user_id);
                        $user = (array) $this->user->getResult();
                        $user['roles'] = $this->user->getRoles();
                        $details = (\Config\Services::JWT())::encode($user);
                        $this->token = $details['jwt'];
                        $this->tokenExpiration = $details['exp'];
                        $this->successed = true;
                        $this
                            ->set('current_token', $this->token)
                            ->where('user_id', $row->user_id)
                            ->update();
                        break;
                }
            } else {
                $this->successed = false;
                $this->error = "Invalid user";
                $this->errorCode = 401;
                $this->errorDescription = "invalid_client";
            }
        } else {
            $this->successed = false;
            $this->error = "Username and/or password NULL";
            $this->errorCode = 400;
            $this->errorDescription = "no_data";
        }
    }

    public function successed(): bool
    {
        return $this->successed;
    }

    public function user()
    {
        return $this->user;
    }

    public function getToken(){
        return $this->token;
    }

    public function getTokenExpiration(){
        return $this->tokenExpiration;
    }

    public function cause(): string
    {
        return $this->error;
    }

    public function errorCode(): int
    {
        return $this->errorCode;
    }

    public function errorDescription(): string
    {
        return $this->errorDescription;
    }
}
