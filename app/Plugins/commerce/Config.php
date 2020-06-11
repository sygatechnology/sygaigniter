<?php
namespace Plugin\commerce;

use \Plugin\BasePluginConfig;
use \Plugin\cms\Services\ObjectTypesService;
use \App\Models\RoleModel;
use \App\Entities\Role;
use \App\Models\CapabilityModel;

class Config extends BasePluginConfig
{
    protected $name = "Syga E-commerce";
    protected $description = "Commerce en ligne";
    protected $version = "1.0.0-alpha.1";
    protected $author = "Syga Technology Team Developer";
    protected $dependencies = [
        "cms" => "1.0.0"
    ];
    protected $helpers = [
        'functions'
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
        $this->deleteUserPosts();
        $this->deleteUserMeta();
        $this->deleteTerms();
        $this->deleteUsers();
        $this->deleteUserRoles();
        $this->deleteCapabilities();
        $this->deleteRoleCapabilities();
        $this->deleteTables();
    }

    /**
     * Installation actions
     */

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
            "user_id" => [
                'type' => 'BIGINT',
                'constraint' => 20
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
                'type' => 'VARCHAR',
                'constraint' => '20',
                'collate' => 'utf8mb4_unicode_ci',
                'default' => ''
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
            "created_at DATETIME DEFAULT CURRENT_TIMESTAMP",
            "updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP",
            "last_loged_on DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP",
            "deleted_at" => [
                'type' => 'DATETIME'
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
        $cityForge->addKey('city_id', TRUE);
        $cityForge->addKey(['name', 'slug']);
        $cityForge->dropTable('sc_cities', TRUE);
        $cityForge->createTable('sc_cities');
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

        $termData = [
            [
                "name" => "Mode",
                "slug" => "sc-mode"
            ],
            [
                "name" => "Auto & Industrie",
                "slug" => "sc-car-industry"
            ],
            [
                "name" => "Immobilier",
                "slug" => "sc-property"
            ],
            [
                "name" => "Cuisine & Maison",
                "slug" => "sc-home-kitchen"
            ],
            [
                "name" => "High-Tech",
                "slug" => "sc-high-tech"
            ],
            [
                "name" => "Informatique & Bureau",
                "slug" => "sc-office-info"
            ],
            [
                "name" => "Beauté, Santé & Bien-être",
                "slug" => "sc-beauty-health-wellness"
            ],
            [
                "name" => "Musique, Films & Jeux vidéo",
                "slug" => "sc-music-films-games"
            ],
            [
                "name" => "Livres & E-books",
                "slug" => "sc-books"
            ],
            [
                "name" => "Autres",
                "slug" => "sc-others"
            ]
        ];
        $termBuilder->insertBatch($termData);
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

    /**
     * Uninstallation actions
     */

    private function deleteOptions()
    {
        /*$names = [
            'commerce_default_comment_status',
            'commerce_default_category'
        ];
        $optionObject = \Config\Services::options();
        $optionObject->delete($names);*/
    }

    private function deleteUserPosts()
    {
        $db = \Config\Database::connect();
        $userSubquery = $db
                            ->table('user_roles')
                            ->select('user_id')
                            ->where('role_slug', 'sc_member')
                            ->getCompiledSelect();
        $postSubquery = $db
                            ->table('posts')
                            ->select('post_id')
                            ->where("`post_author` IN ($userSubquery)", NULL, FALSE)
                            ->getCompiledSelect();
                        
        $db->table('term_relationships')
                    ->where("`object_id` IN ($postSubquery)", NULL, FALSE)
                    ->delete();

        $db->table('postmeta')
                    ->where("`post_id` IN ($postSubquery)", NULL, FALSE)
                    ->delete();

        $db->table('posts')
                    ->where("`post_author` IN ($userSubquery)", NULL, FALSE)
                    ->delete();
    }

    private function deleteUserMeta()
    {
        /*$db = \Config\Database::connect();
        $userSubquery = $db
                            ->table('user_roles')
                            ->select('user_id')
                            ->where('role_slug', 'sc_member')
                            ->getCompiledSelect();*/
    }

    private function deleteTerms()
    {
        $db = \Config\Database::connect();
        $terms = [
            "sc-mode",
            "sc-car-industry",
            "sc-property",
            "sc-home-kitchen",
            "sc-high-tech",
            "sc-office-info",
            "sc-beauty-health-wellness",
            "sc-music-films-games",
            "sc-books",
            "sc-others"
        ];
        $termSubquery = $db
                            ->table('terms')
                            ->select('term_id')
                            ->whereIn('role_slug', $terms)
                            ->getCompiledSelect();

        $db->table('term_taxonomies')
                            ->where("`term_id` IN ($termSubquery)", NULL, FALSE)
                            ->delete();

        $db->table('terms')->whereIn('slug', $terms)->delete();
    }

    private function deleteUsers(){
        $db = \Config\Database::connect();
        $userSubquery = $db
                            ->table('user_roles')
                            ->select('user_id')
                            ->where('role_slug', 'sc_member')
                            ->getCompiledSelect();

        $db
            ->table('users')
            ->where("`user_id` IN ($userSubquery)", NULL, FALSE)
            ->delete();
    }

    private function deleteTables(){
        $forge = \Config\Database::forge();
        $forge->dropTable('sc_users', TRUE);
        $forge->dropTable('sc_usermeta', TRUE);
        $forge->dropTable('sc_cities', TRUE);
    }

    private function deleteUserRoles(){
        $roles = [
            "sc_member"
        ];
        $db = \Config\Database::connect();
        $db->table('roles')->whereIn("slug", $roles)->delete();
    }

    private function deleteRoleCapabilities(){
        $roles = [
            "sc_member"
        ];
        $db = \Config\Database::connect();
        $db->table('role_capabilities')->whereIn("role_slug", $roles)->delete();
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
    
}
