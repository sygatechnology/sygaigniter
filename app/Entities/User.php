<?php namespace App\Entities;

/**
 * @package    App\Entities
 * @author     SygaTechnology Dev Team
 * @copyright  2019 SygaTechnology Foundation
 */

use CodeIgniter\I18n\Time;
use \App\Core\SY_Entity;

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
        'lname' => null,
        'email' => null,
        'psswd' => null,
        'status' => "pending",
    ];

    protected $datamap = [
        'id' => 'user_id',
        'username' => 'uname',
        'firstname' => 'fname',
        'lastname' => 'lname',
        'password' => 'psswd',
    ];

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    protected $casts = [
        'options' => 'array',
        'options_object' => 'json',
        'options_array' => 'json-array',
    ];

    /**
     * Allows filling in Entity parameters during construction.
     *
     * @param array|null $data
     */

    protected $now;
    protected $nowPlusTwoDays;

    protected $usePublicAttributes = true;
    protected $publicAttributes = [
        "id",
        "email",
        "username",
        "firstname",
        "lastname", /*,
        "status",
        "created_at",
        "updated_at"*/
    ];

    // Cette valeur doit être identique
    //à celle qui se trouve dans les règles de validation dans le UserModel
    private $minPassordLength = 8;

    public function __construct(array $data = null)
    {
        parent::__construct($data);
        helper('user');
        helper('security');
        $this->now = (new Time('now'))->toDateTimeString();
        $this->nowPlusTwoDays = (new Time('+2 day'))->toDateTimeString();
    }

    public function setPsswd(string $pass)
    {
        $pass = trim($pass);
        if (mb_strlen($pass) >= $this->minPassordLength) {
            $this->attributes['psswd'] = set_password($pass);
            $this->setResetPsswdToken();
            $this->setResetPsswdValidity();
        } else {
            $this->attributes['psswd'] = $pass;
        }
        return $this;
    }

    public function setUname(string $name)
    {
        $this->attributes['uname'] = slugify($name, '_');
        return $this;
    }

    public function setFirstname(string $fname)
    {
        $this->attributes['fname'] = $fname;
        return $this;
    }

    public function setLastname(string $lname)
    {
        $this->attributes['lname'] = $lname;
        return $this;
    }

    public function getRoles(): array
    {
        $db = \Config\Database::connect();
        $rows = $db->table('user_roles')->join('roles', 'roles.slug = user_roles.role_slug')->where('user_roles.user_id', $this->attributes['user_id'])->get()->getResult();
        $result = [];
        foreach ($rows as $row) {
            $result[$row->slug] = $row->label;
        }
        unset($rows);
        return $result;
    }

    private function setResetPsswdToken()
    {
        $this->attributes['reset_psswd_token'] = safe_b64encode($this->attributes['email'] . "@@" . $this->nowPlusTwoDays);
    }

    private function setResetPsswdValidity()
    {
        $this->attributes['reset_psswd_validity'] = $this->nowPlusTwoDays;
    }
}
