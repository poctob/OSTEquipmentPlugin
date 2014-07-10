<link rel="stylesheet" href="../assets/default/css/theme_equipment.css" media="screen">
<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of equipment
 *
 * @author Alex Pavlunenko <alexp at xpresstek.net>
 */
require_once(INCLUDE_DIR . 'class.plugin.php');
require_once(INCLUDE_DIR . 'class.signal.php');
require_once(INCLUDE_DIR . 'class.app.php');

require_once('config.php');

define('EQUIPMENT_TABLE',TABLE_PREFIX.'equipment');
define('EQUIPMENT_CATEGORY_TABLE',TABLE_PREFIX.'equipment_category');
define('EQUIPMENT_STATUS_TABLE',TABLE_PREFIX.'equipment_status');
define('EQUIPMENT_TICKET_TABLE',TABLE_PREFIX.'equipment_ticket');
define('EQUIPMENT_TICKET_VIEW',TABLE_PREFIX.'EquipmentTicketView');
define('PLUGINS_ROOT',INCLUDE_DIR.'plugins/');

define('EQUIPMENT_PLUGIN_ROOT',__DIR__.'/');
define('EQUIPMENT_INCLUDE_DIR',EQUIPMENT_PLUGIN_ROOT.'include/');
define('EQUIPMENT_STAFFINC_DIR',EQUIPMENT_INCLUDE_DIR.'staff/');
define('EQUIPMENT_CLIENTINC_DIR',EQUIPMENT_INCLUDE_DIR.'client/');

require_once(EQUIPMENT_INCLUDE_DIR . 'class.equipment_install.php');

class EquipmentPlugin extends Plugin {

    var $config_class = 'EquipmentConfig';

    function bootstrap() {
        if ($this->firstRun()) {
            $this->configureFirstRun();
        }

        $config = $this->getConfig();

        if ($config->get('equipment_backend_enable')) {
            $this->createStaffMenu();

            if ($config->get('equipment_frontend_enable')) {
                $this->createFrontMenu();
            }
        }
      //  Signal::connect('model.updated', array('EquipmentPlugin', 'callback'));
    }

    /**
     * Creates menu links in the staff backend.
     */
    function createStaffMenu() {

        Application::registerStaffApp('Equipment', 
                'equipment.php', 
                array(iconclass => 'equipment'));
        Application::registerStaffApp('Equipment Categories', 
                'equipment_categories.php', 
                array(iconclass => 'faq-categories'));
        Application::registerStaffApp('Equipment Status', 
                'equipment_status.php', 
                array(iconclass => 'equipment_status'));
    }

    /**
     * Creates menu link in the client frontend.
     * Useless as of OSTicket version 1.9.2.
     */
    function createFrontMenu() {
        Application::registerClientApp('Equipment Status', 
                'equipment_front/index.php', 
                array(iconclass => 'equipment'));
    }

    /**
     * Checks if this is the first run of our plugin.
     * @return boolean
     */
    function firstRun() {
        $sql='SHOW TABLES LIKE \''.EQUIPMENT_TABLE.'\'';  
        $res=db_query($sql);
        return  (db_num_rows($res)==0);                
    }

    /**
     * Necessary functionality to configure first run of the application
     */
    function configureFirstRun() {
       if(!$this->createDBTables())
       {
           echo "First run configuration error.  "
            . "Unable to create database tables!";
       }
    }

    /**
     * Kicks off database installation scripts
     * @return boolean
     */
    function createDBTables() {
       $installer = new EquipmentInstaller();
       return $installer->install();
        
    }
    
    /**
     * Uninstall hook.
     * @param type $errors
     * @return boolean
     */
    function pre_uninstall(&$errors) {
       $installer = new EquipmentInstaller();
       return $installer->remove();
    }
  
}
