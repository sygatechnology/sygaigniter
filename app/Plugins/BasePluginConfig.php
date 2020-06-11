<?php
    namespace Plugin;
    use App\API\v1\Services\PluginsService;
    abstract class BasePluginConfig {

        protected $name = "";
        protected $description = "";
        protected $version = "";
        protected $author = "Syga Technology Team Developer";
        protected $dependencies = [];
        protected $helpers = [];

        public function __construct(){
            $reflection = new \ReflectionClass(get_called_class());
            $definitionPath = $reflection->getFileName();
            $this->basename = basename(realpath(dirname($definitionPath)));
            
            if(PluginsService::isEnabled($this->basename)){
                if(! empty($this->helpers)) $this->loadHelper($this->helpers);
                $this->pluginDidMount();
            }
        }

        protected function loadHelper($filenames){
            if (! is_array($filenames))
            {
                $filenames = [$filenames];
            }
            $helpers = [];
            foreach($filenames as $filename){
                $helpers[] = 'Plugin\\'.$this->basename.'\Helpers\\'.$filename;
            }
            helper($helpers);
        }

        abstract public function pluginDidMount();

        public function getName(){
            return trim($this->name) !== '' ? $this->name : $this->basename;
        }

        public function getDescription(){
            return $this->description;
        }

        public function getVersion(){
            return $this->version;
        }

        public function getAuthor(){
            return $this->author;
        }

        abstract public function pluginDidInstall();

        abstract function pluginDidUninstall();
    }
