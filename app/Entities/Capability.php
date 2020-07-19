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

class Capability extends SY_Entity
{
    protected $attributes = [
        'capability_id' => null,
        'slug' => null,
        'label' => null,
        "deleted_at" => null
    ];

    protected $datamap = [
        'id' => 'capability_id'
    ];

    protected $dates = ['deleted_at'];

    public function setLabel(string $label)
    {
      helper('inflector');
      $this->attributes['label'] = humanize($label, ' ');
      return $this;
    }

    public function setSlug(string $slug)
    {
      $this->attributes['slug'] = slugify($slug, '_');
      return $this;
    }
}
