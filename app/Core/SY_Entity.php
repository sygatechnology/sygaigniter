<?php namespace App\Core;

/**
 * @package    App\Entities
 * @author     SygaTechnology Dev Team
 * @copyright  2019 SygaTechnology Foundation
 */

use CodeIgniter\Entity;

class SY_Entity extends Entity
{
    public function __construct(array $data = null)
    {
        parent::__construct($data);
    }
}
