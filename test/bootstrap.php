<?php
define('INCLUDE_DIR', '/var/www/html/ost193/include/');
define('EQUIPMENT_PLUGIN_ROOT', __DIR__ . '/');
include_once('AutoLoader.php');
//include_once('include/model/Entity.php');

 function autoload($className) {
        $className = ltrim($className, '\\');
        $fileName = '';
        $namespace = '';
        if ($lastNsPos = strrpos($className, '\\')) {
            $namespace = substr($className, 0, $lastNsPos);
            $className = substr($className, $lastNsPos + 1);
            $fileName = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
        }
        $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
        $fileName = 'include/' . $fileName;

        if (file_exists('../' . $fileName)) {
            require '../'.$fileName;
        }
        else  if (file_exists(INCLUDE_DIR . $fileName)) 
        {
            require INCLUDE_DIR . $fileName;
        }
        else  if (file_exists(EQUIPMENT_PLUGIN_ROOT . $fileName)) 
        {
            require EQUIPMENT_PLUGIN_ROOT . $fileName;
        }
          else  if (file_exists( $fileName)) 
        {
            require $fileName;
        }
    }
spl_autoload_register('autoload');
?>

