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
            $file = $this->request->getFile('plugin_file');
            $activate = ($this->request->getVar('activate') == 'yes') ? true : false;
            if ($pluginName = PluginsService::install($file, $activate)) {
                return $this->respondCreated([$pluginName . ' plugin installed']);
            }
            return $this->fail('Unsupported media type', 415);
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
