<?php

namespace QNews\src;


use QNews\src\core\init\TD_QNews_Init;
use QNews\src\services\TD_QNews_ClassLoader;

if(!class_exists('TD_QNews')) {
    class TD_QNews
    {
        protected static $instance;

        public static function getInstance()
        {
            if(!self::$instance)
            {
                self::$instance = new self;
            }
            return self::$instance;
        }

        private function __construct(){

            register_activation_hook(TD_QNEWS_ROOT_PATH,array($this,'activate'));
            register_deactivation_hook(TD_QNEWS_ROOT_PATH,array($this,'deactivate'));
            $this->init_autoloader();
            add_action('plugins_loaded',array($this,'init_plugin_core'),0);
        }

        function init_plugin_core()
        {
            TD_QNews_Init::getInstance()->init();
        }

        function activate()
        {

        }
        function deactivate()
        {

        }

        private function init_autoloader()
        {
            if(!class_exists('TD_QNews_ClassLoader'))
            {
                require_once TD_QNEWS_PLUGIN_DIR.'src/services/TD_QNews_ClassLoader.php';
            }

            $autoLoader = TD_QNews_ClassLoader::getInstance();
            $autoLoader->setPrefixes(array("QNews"=>TD_QNEWS_PLUGIN_DIR));
            $autoLoader->register_auto_loader();
        }
    }
}
