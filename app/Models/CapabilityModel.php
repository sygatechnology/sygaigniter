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

class CapabilityModel extends SY_Model
{
    protected $table = 'capabilities';
    protected $primaryKey = 'capability_id';

    protected $returnType = 'App\Entities\Capability';
    protected $useSoftDeletes = true;

    protected $allowedFields = ['slug', 'label'];

    protected $useTimestamps = false;
    protected $deletedField = 'deleted_at';

    protected $skipValidation = false;
    protected $validationRules = [
        'slug' => 'required|is_unique[capabilities.slug]|alpha_dash|min_length[4]',
        'label' => 'required|min_length[4]',
    ];
}
