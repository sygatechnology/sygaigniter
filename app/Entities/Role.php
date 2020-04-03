<?php namespace App\Entities;

/**
* @package    App\Entities
* @author     SygaTechnology Dev Team
* @copyright  2019 SygaTechnology Foundation
*/

use \App\Core\SY_Entity;
use CodeIgniter\I18n\Time;

/**
 * Class User
 *
 * @todo Users Resource Entity
 *
 * @package App\Entities
 */

class Role extends SY_Entity
{
    protected $attributes = [
        'role_id' => null,
        'slug' => null,
        'label' => null,
        'is_default' => 0,
        "created_at" => null,
        "updated_at" => null,
        "deleted_at" => null
    ];

    protected $datamap = [
        'id' => 'role_id',
        'default' => 'is_default'
    ];

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    protected $casts = [
        'default' => 'boolean',
        'options' => 'array',
        'options_object' => 'json',
        'options_array' => 'json-array'
    ];

    /**
  	 * Allows filling in Entity parameters during construction.
  	 *
  	 * @param array|null $data
  	 */

     protected $usePublicAttributes = true;
     protected $publicAttributes = [
       "id",
       "slug",
       "label",
       "default"
     ];

    public function setLabel(string $label)
    {
        helper('inflector');
        $this->attributes['label'] = humanize($label, ' ');
        $this->attributes['slug'] = slugify($label, '_');
        return $this;
    }

    public function setSlug(string $slug)
    {
        helper('inflector');
        $this->attributes['slug'] = slugify($slug, '_');
        return $this;
    }

    public function getSlug()
    {
        return $this->attributes['slug'];
    }

    public function isDefauult()
    {
        return isset($this->attributes['is_default']) && (int) $this->attributes['is_default'] === 1;
    }
}
