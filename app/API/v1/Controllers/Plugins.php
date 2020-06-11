<?php

namespace App\API\v1\Controllers;

/**
 * @package    App\Controller\Api
 * @author     SygaTechnology Dev Team
 * @copyright  2019 SygaTechnology Foundation
 */

use App\Controllers\BaseController;
use \App\API\v1\Services\FilesService;
use App\API\v1\Services\PluginsService;

class Plugins extends BaseController
{
    public function index()
    {
        if ($this->currentUser->isAuthorized("list_plugins")) {
            $params = \Config\Services::apiRequest();
            $limitVar = $params->getParam('limit');
            $offsetVar = $params->getParam('page');
            $limit = $limitVar !== null ? (int) $limitVar : 10;
            $offset = $offsetVar !== null ? (int) $offsetVar : 1;
            $plugins = PluginsService::get(null, $limit, $offset);
            return $this->respond($plugins, 200);
        }
        return $this->failForbidden("List plugins capability required");
    }

    public function install()
    {
        if ($this->currentUser->isAuthorized("install_plugin")) {
            $file = $this->request->getFile('plugin');
            $activate = ($this->request->getVar('activate') == 'yes') ? true : false;
            if ($pluginName = PluginsService::install($file, $activate)) {
                return $this->respondCreated([$pluginName . ' plugin installed']);
            }
            return $this->fail('Unsupported media type', 415);
        }
        return $this->failForbidden("Install plugin capability required");
    }

    public function activateDeactivate()
    {
        if ($this->currentUser->isAuthorized("manage_plugin")) {
            $params = \Config\Services::apiRequest();
            $plugin = $params->getParam('plugin');
            if($plugin !== null){
                $action = $params->getParam('action');
                if($action !== null && ( $action == 'activate' || $action == 'deactivate') ) {
                    $activate = $action ? true : false;
                    $autoload = ($params->getParam('autoload') == 'yes') ? true : false;
                    if ($pluginName = PluginsService::save($plugin, $activate, $autoload)) {
                        return $this->respond([$pluginName . ' plugin ' . $activate ? 'enabled' : 'disabled'], 200);
                    }
                    return $this->fail('Error saving', 400);
                }
                return $this->fail('Unsupported action value', 415);
            }
            return $this->fail('Unsupported plugin value', 415);            
        }
        return $this->failForbidden("Install plugin capability required");
    }

    public function delete($name = null)
    {
        if ($this->currentUser->isAuthorized("uninstall_plugin")) {
            $config = PluginsService::config($name);
            if(is_bool($config) === false){
                $config->pluginDidUninstall();
            }
            if (PluginsService::uninstall($name)) {
                return $this->respondDeleted([$name . ' plugin uninstalled']);
            }
            return $this->fail('Plugin not found', 404);
        }
        return $this->failForbidden("Uninstall plugin capability required");
    }
}
