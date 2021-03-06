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

  class RoleModel extends SY_Model
  {
    protected $table                = 'roles';
    protected $primaryKey           = 'role_id';

    protected $returnType           = 'App\Entities\Role';
    protected $useSoftDeletes       = true;

    protected $allowedFields        = ['slug', 'label', 'is_default'];

    protected $useTimestamps        = false;
    protected $createdField         = 'created_at';
    protected $updatedField         = 'updated_at';
    protected $deletedField         = 'deleted_at';

    protected $skipValidation       = false;
    protected $validationRules    = [
        'label' => 'required|is_unique[roles.label]|min_length[4]',
        'slug'  => 'is_unique[roles.slug]'
    ];
    protected $validationMessages = [
        'slug' => [
            'is_unique' => 'Le champ label doit contenir une valeur unique pour en avoir aussi pour le champ slug.'
        ]
    ];

    public function getDefault()
    {
      $role = $this->where('is_default', 1)->get()->findAll();
      return (count($role) > 0) ? $role[0]->slug : null;
    }

    public function exists($slug): bool
    {
      return ($this->where('slug', $slug)->countAllResults() > 0);
    }

  }
