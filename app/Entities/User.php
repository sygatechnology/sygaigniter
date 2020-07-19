<?php namespace App\Entities;

/**
 * @package    App\Entities
 * @author     SygaTechnology Dev Team
 * @copyright  2019 SygaTechnology Foundation
 */

use CodeIgniter\I18n\Time;
use \App\Core\SY_Entity;
use \App\Models\RoleModel;

/**
 * Class User
 *
 * @todo Users Resource Entity
 *
 * @package App\Entities
 */

class User extends SY_Entity
{
    protected $attributes = [
        'lastname' => null,
        'email' => null,
        'password' => null,
        'status' => "pending",
    ];

    protected $datamap = [
        'id' => 'user_id'
    ];

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    /**
     * Allows filling in Entity parameters during construction.
     *
     * @param array|null $data
     */

    protected $now;
    protected $nowPlusTwoDays;

    // Cette valeur doit être identique
    //à celle qui se trouve dans les règles de validation dans le UserModel
    private $minPassordLength = 8;

    public function __construct($data = null)
    {
        if(NULL !== $data && !is_array($data)){
            $data = ['id' => (int) $data]; 
        }
        parent::__construct($data);
        helper('user');
        helper('security');
        $this->now = (new Time('now'))->toDateTimeString();
        $this->nowPlusTwoDays = (new Time('+2 day'))->toDateTimeString();
    }

    public function setPassword(string $pass)
    {
        $pass = trim($pass);
        if (mb_strlen($pass) >= $this->minPassordLength) {
            $this->attributes['password'] = set_password($pass);
            $this->setResetPsswdToken();
            $this->setResetPsswdValidity();
        } else {
            $this->attributes['password'] = $pass;
        }
        return $this;
    }

    public function setUname(string $name)
    {
        $this->attributes['username'] = slugify($name, '_');
        return $this;
    }

    public function setFirstname(string $fname)
    {
        $this->attributes['firstname'] = $fname;
        return $this;
    }

    public function setLastname(string $lname)
    {
        $this->attributes['lastname'] = $lname;
        return $this;
    }

    public function getRoles()
    {
        $roleModel = new RoleModel();
        return  $roleModel
                        ->join('ci_user_roles', 'ci_user_roles.role_slug = ci_roles.slug')
                        ->where('user_roles.user_id', $this->getId())
                        ->findAll();
    }

    private function setResetPsswdToken()
    {
        $this->attributes['reset_psswd_token'] = safe_b64encode($this->attributes['email'] . "@@" . $this->nowPlusTwoDays);
    }

    private function setResetPsswdValidity()
    {
        $this->attributes['reset_psswd_validity'] = $this->nowPlusTwoDays;
    }

    public function getId(){
        return $this->attributes['user_id'];
    }
}
