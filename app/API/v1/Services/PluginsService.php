<?php namespace App\API\v1\Services;

use CodeIgniter\Config\BaseService;
use \App\API\v1\Services\FilesService;

class PluginsService extends BaseService
{
    private static $pluginOptionName = 'plugins';
    protected static $registered = [];

    public static function register($name, $status)
    {
        if (!isset(self::$registered[$name])) {
            self::$registered[$name] = $status;
        } else {
            throw new \InvalidArgumentException('The plugin name ' . $name . ' already exists.');
        }
    }

    public static function get($status = null, $limit = 10, $numPage = 1)
    {
        $plugins = [];
        $fromPath = self::getFromPath();
        $fromOption = self::getFromOption();
        foreach ($fromPath as $slug => $meta) {
            $meta['slug'] = $slug;
            if (isset($fromOption[$slug])) {
                $meta['enabled'] = $fromOption[$slug] == 'enabled' ? true : false;
            } else {
                $meta['enabled'] = false;
            }
            if (!is_null($status) && $status == 'enabled') {
                if (isset($fromOption[$slug]) && $fromOption[$slug] == $status) {
                    $plugins[] = $meta;
                } else {
                    if ($status == 'disabled') {
                        $plugins[] = $meta;
                    }
                }
            } else {
                $plugins[] = $meta;
            }
        }
        $result = \Config\Services::ApiResult();
        return $result->setFromRows($plugins, $limit, $numPage);
    }

    public static function getWhereNameIs($pluginName)
    {
        $plugins = (array) self::get('enabled', 0);
        foreach($plugins['data'] as $plugin){
            $plugin = (array) $plugin;
            if($plugin['slug'] == $pluginName){
                return $plugin;
            }
        }
        return null;
    }

    public static function isEnabled($pluginName){
        $fromOption = self::getFromOption();
        return isset($fromOption[$pluginName]) && $fromOption[$pluginName] == 'enabled';
    }

    private static function getFromPath()
    {
        $plugins = [];
        foreach (FilesService::listDirs(PLUGINS_PATH) as $plugin) {
            $segment = explode(DIRECTORY_SEPARATOR, $plugin);
            $slug = end($segment);
            $plugins[$slug] = PluginsService::getMeta($slug);
        }
        return $plugins;
    }

    private static function getMeta($slug)
    {
        if ($config = self::config($slug)) {
            return [
                'name' => $config->getName(),
                'description' => $config->getDescription(),
                'version' => $config->getVersion(),
                'author' => $config->getAuthor(),
                'dir' => $slug,
            ];
        }
        return [
            'name' => $slug,
            'description' => "",
            'version' => "1.0.0",
            'author' => "",
            'dir' => $slug,
        ];
    }

    public static function config($slug)
    {
        if (is_file(PLUGINS_PATH . $slug . DIRECTORY_SEPARATOR . 'Config.php')) {
            $class = '\Plugin\\' . $slug . '\\Config';
            return new $class();
        }
        return false;
    }

    public static function install($file, $activate = false)
    {
        if ($file->isValid() && !$file->hasMoved() && strtolower($file->getExtension()) == 'zip') {
            if ($file->move(WRITEPATH . 'uploads', null, true)) {
                if (!is_dir(WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . 'tmp')) {
                    mkdir(WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . 'tmp');
                }
                $location = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . $file->getName();
                $name = FilesService::getFilenameWithoutExtension($file->getName());
                $newLocation = WRITEPATH . 'uploads'. DIRECTORY_SEPARATOR .'tmp' . DIRECTORY_SEPARATOR . $name;
                if (FilesService::unzip($location, $newLocation)) {
                    unlink($location);
                    if ($pluginName = self::movePluginFiles($newLocation, PLUGINS_PATH . $name)) {
                        $config = self::config($pluginName);
                        if ($config) {
                            $config->pluginDidInstall();
                        }
                        self::save($pluginName, $activate);
                        return $pluginName;
                    }
                    return false;
                }
                return false;
            }
        }
        return false;
    }

    private static function save($name, $activate)
    {
        $fromOption = self::getFromOption();
        if (isset($fromOption[$name])) {
            $fromOption[$name] = ($fromOption[$name] == 'disabled' && $activate) ? 'enabled' : 'disabled';
        } else {
            $fromOption[$name] = $activate ? 'enabled' : 'disabled';
        }
        return \App\API\v1\Services\OptionsService::save(self::$pluginOptionName, $fromOption, true);
    }

    public static function getFromOption()
    {
        $fromOption = \App\API\v1\Services\OptionsService::get(self::$pluginOptionName);
        return ($fromOption != null) ? $fromOption : [];
    }

    public static function exists($name): bool
    {
        $fromOption = self::getFromOption();
        return isset($fromOption[$name]);
    }

    private static function movePluginFiles($location, $newLocation)
    {
        $files = FilesService::readDir($location);
        $segment = explode(DIRECTORY_SEPARATOR, $location);
        $dirname = end($segment);
        if (count($files) === 1) {
            $location = $files[0];
            $locationSegment = explode(DIRECTORY_SEPARATOR, $location);
            $newLocationSegment = explode(DIRECTORY_SEPARATOR, $newLocation);
            array_pop($newLocationSegment);
            $newLocation = "";
            foreach($newLocationSegment as $file){
                $newLocation .= $file . DIRECTORY_SEPARATOR;
            }
            $newLocation .= end($locationSegment);
            $dirname = end($locationSegment);
        }
        if(is_dir($newLocation)){
            self::uninstall($dirname);
        }
        FilesService::copy($location, $newLocation);
        FilesService::removeDir($location);
        return $dirname;
    }

    public static function uninstall($name)
    {
        $fromOption = self::getFromOption();
        unset($fromOption[$name]);
        \App\API\v1\Services\OptionsService::update(self::$pluginOptionName, $fromOption);
        if (file_exists(PLUGINS_PATH . $name)) {
            $config = self::config($name);
            if ($config) {
                $config->pluginDidUninstall();
            }
            return FilesService::removeDir(PLUGINS_PATH . $name);
        }
        return false;
    }

    public static function load($name)
    {
        if (isset(self::$registered[$name])) {
            return self::$registered[$name];
        }
        throw new \InvalidArgumentException('No plugin name ' . $name . ' registered');
    }

}
