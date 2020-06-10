<?php
namespace Plugin\commerce;

use \Plugin\BasePluginConfig;
use \Plugin\crm\Services\ObjectTypesService;
use \Plugin\crm\Services\TaxonomiesService;
use \App\Models\RoleModel;
use \App\Entities\Role;
use \App\Models\CapabilityModel;
use CodeIgniter\I18n\Time;

class Config extends BasePluginConfig
{
    protected $name = "Syga E-commerce";
    protected $description = "Commerce en ligne";
    protected $version = "1.0.0-alpha.1";
    protected $author = "Syga Technology Team Developer";
    protected $helpers = [
        'functions',
        'post',
        'term',
    ];

    public function pluginDidInstall()
    {
        $this->createTables();
        $this->registerOptions();
        $this->registerUserRoles();
        $this->registerCapabilities();
        $this->registerRoleCapabilities();

        $this->createTermsAndPostsAndSetDefaultCategoryOption();
    }

    public function pluginDidUninstall()
    {
        $this->deleteOptions();
        $this->deleteTables();
        $this->deleteUserRoles();
        $this->deleteCapabilities();
        $this->deleteRoleCapabilities();
    }

    private function createTables()
    {
        // Users table
        $userForge = \Config\Database::forge();
        $userFields = [
            "id" => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'auto_increment' => true
            ],
            "fire_id" => [
                'type' => 'TEXT',
                'collate' => 'utf8mb4_unicode_ci'
            ],
            "login" => [
                'type' => 'VARCHAR',
                'constraint' => '200',
                'collate' => 'utf8mb4_unicode_ci',
                'default' => ''
            ],
            "email" => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'collate' => 'utf8mb4_unicode_ci',
                'default' => ''
            ],
            "real_name" => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'collate' => 'utf8mb4_unicode_ci',
                'default' => ''
            ],
            "birthday" => [
                'type' => 'DATE',
                'default' => '0000-00-00'
            ],
            "gender" => [
                'type' => 'VARCHAR',
                'constraint' => '5',
                'collate' => 'utf8mb4_unicode_ci',
                'default' => ''
            ],
            "item_count" => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'default' => 0
            ],
            "followers_count" => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'default' => 0
            ],
            "following_count" => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'default' => 0
            ],
            "positive_feedback_count" => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'default' => 0
            ],
            "neutral_feedback_count" => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'default' => 0
            ],
            "negative_feedback_count" => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'default' => 0
            ],
            "meeting_transaction_count" => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'default' => 0
            ],
            "account_status" => [
                'type' => 'VARCHAR',
                'constraint' => '20',
                'collate' => 'utf8mb4_unicode_ci',
                'default' => 'pending'
            ],
            "feedback_reputation" => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'default' => 0
            ],
            "expose_location" => [
                'type' => 'INT',
                'constraint' => 20,
                'default' => 0
            ],
            "city_id" => [
                'type' => 'INT',
                'constraint' => 20,
                'default' => 0
            ],
            "created_at" => [
                'type' => 'DATETIME',
                'default' => '0000-00-00 00:00:00',
            ],
            "updated_at" => [
                'type' => 'DATETIME',
                'default' => '0000-00-00 00:00:00',
            ],
            "last_loged_on" => [
                'type' => 'DATETIME',
                'default' => '0000-00-00 00:00:00',
            ],
            "deleted_at" => [
                'type' => 'DATETIME',
                'default' => '0000-00-00 00:00:00',
            ]
        ];
        $userForge->addField($userFields);
        $userForge->addKey('id', TRUE);
        $userForge->addKey(['login', 'email', 'account_status', 'created_at']);
        $userForge->dropTable('sc_users', TRUE);
        $userForge->createTable('sc_users');

        // User meta table
        $userMetaForge = \Config\Database::forge();
        $userMetafields = [
            'meta_id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true
            ],
            'meta_key' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'collate' => 'utf8mb4_unicode_ci',
                'null' => true
            ],
            'meta_value' => [
                'type' => 'LONGTEXT',
                'collate' => 'utf8mb4_unicode_ci',
                'null' => true
            ]
        ];
        $userMetaForge->addField($userMetafields);
        $userMetaForge->addKey('meta_id', TRUE);
        $userMetaForge->addKey(['user_id', 'meta_key']);
        $userMetaForge->dropTable('sc_usermeta', TRUE);
        $userMetaForge->createTable('sc_usermeta');

        // Cities table
        $cityForge = \Config\Database::forge();
        $cityFields = [
            'city_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => '200',
                'collate' => 'utf8mb4_unicode_ci',
                'default' => ''
            ],
            'slug' => [
                'type' => 'VARCHAR',
                'constraint' => '200',
                'collate' => 'utf8mb4_unicode_ci',
                'default' => ''
            ]
        ];
        $cityForge->addField($cityFields);
        $termForge->addKey('city_id', TRUE);
        $termForge->addKey(['name', 'slug']);
        $termForge->dropTable('sc_cities', TRUE);
        $termForge->createTable('sc_cities');
    }

    private function registerOptions()
    {
        //$optionObject = \Config\Services::options();
        //foreach ([
        //    'commerce_default_comment_status' => [
        //        'post' => 'open',
        //        'page' => 'closed'
        //    ],
        //    'commerce_default_category' => 'non-assignee'
        //] as $name => $value) {
        //    $optionObject->add($name, $value, true);
        //}
    }

    private function registerUserRoles(){
        $roles = [
            [
                "label" => "Membre E-commerce",
                "slug" => "sc_member"
            ]
        ];
        foreach ($roles as $data) {
            $role = new Role();
            $role->fill($data);
            $roleModel = new RoleModel();
            if(! $roleModel->exists($role->getSlug())){
                $roleModel->insert($role);
            }
        }
    }

    private function registerCapabilities(){
        $roleCapabilities = [
            [
                "slug" => "edit_product",
                "label" => "Éditer un produit"
            ],
            [
                "slug" => "delete_product",
                "label" => "Supprimer un produit"
            ],
            [
                "slug" => "publish_products",
                "label" => "Pulblier des produits"
            ]
        ];
        $capabilityModel = new CapabilityModel();
        $capabilityModel->insertBatch($roleCapabilities);
    }

    private function registerRoleCapabilities(){
        $roleCapabilities = [
            "editor" => [
                "edit_product",
                "delete_product",
                "publish_products",
                "edit_service",
                "delete_service",
                "publish_services"
            ],
            "sc_member" => [
                "edit_product",
                "delete_product",
                "publish_products",
                "edit_service",
                "delete_service",
                "publish_services"
            ]
        ];
        $data = [];
        foreach ($roleCapabilities as $role => $caps) {
            foreach ($caps as $cap) {
                $data[] = [
                    'role_slug' => $role,
                    'capability_slug' => $cap
                ];
            }
        }
        $db = \Config\Database::connect();
        $db->table('role_capabilities')->insertBatch($data);
    }

    private function createTermsAndPostsAndSetDefaultCategoryOption(){
        $db = \Config\Database::connect();
        $termBuilder = $db->table('terms');

        $categoryData = [
            [
                "name" => "Mode",
                "slug" => "mode"
            ],
            [
                "name" => "Auto & Industrie",
                "slug" => "car-industry"
            ],
            [
                "name" => "Immobilier",
                "slug" => "property"
            ],
            [
                "name" => "Cuisine & Maison",
                "slug" => "home-kitchen"
            ],
            [
                "name" => "High-Tech",
                "slug" => "high-tech"
            ],
            [
                "name" => "Informatique & Bureau",
                "slug" => "office-info"
            ],
            [
                "name" => "Beauté, Santé & Bien-être",
                "slug" => "beauty-health-wellness"
            ],
            [
                "name" => "Musique, Films & Jeux vidéo",
                "slug" => "music-films-games"
            ],
            [
                "name" => "Livres & E-books",
                "slug" => "books"
            ],
            [
                "name" => "Autres",
                "slug" => "others"
            ],
        ];
        $termBuilder->insertBatch($categoryData);
    }

    private function deleteOptions()
    {
        /*$names = [
            'commerce_default_comment_status',
            'commerce_default_category'
        ];
        $optionObject = \Config\Services::options();
        $optionObject->delete($names);*/
    }

    private function deleteTables(){
        $forge = \Config\Database::forge();
        $forge->dropTable('sc_users', TRUE);
        $forge->dropTable('sc_cities', TRUE);
    }

    private function deleteUserRoles(){
        $roles = [
            "sc_member"
        ];
        $db = \Config\Database::connect();
        $db->table('roles')->whereIn("slug", $roles)->delete();
    }

    private function deleteCapabilities(){
        $caps = [
            "edit_product",
            "delete_product",
            "publish_products",
            "edit_service",
            "delete_service",
            "publish_services"
        ];
        $db = \Config\Database::connect();
        $db->table('capabilities')->whereIn("slug", $caps)->delete();
    }

    private function deleteRoleCapabilities(){
        $roles = [
            "sc_member"
        ];
        $db = \Config\Database::connect();
        $db->table('role_capabilities')->whereIn("role_slug", $roles)->delete();
    }

    public function pluginDidMount()
    {
        $this->registerObjectTypes();
    }
    
    private function registerObjectTypes()
    {
        ObjectTypesService::register('product', array(
            'hierarchical' => false,
            'supports' => [
                'title' => true,
                'editor' => true,
                'category' => true,
                'tag' => true
            ],
            'capability_type' => 'post'
        ));
        ObjectTypesService::register('service', array(
            'hierarchical' => false,
            'supports' => [
                'title' => true,
                'editor' => true,
                'category' => true,
                'tag' => true
            ],
            'capability_type' => 'post'
        ));
    }
}
