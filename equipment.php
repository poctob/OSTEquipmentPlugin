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
require_once(INCLUDE_DIR . 'class.dispatcher.php');
require_once(INCLUDE_DIR . 'class.dynamic_forms.php');
require_once(INCLUDE_DIR . 'class.osticket.php');

require_once('config.php');

define('EQUIPMENT_TABLE',TABLE_PREFIX.'equipment');
define('EQUIPMENT_CATEGORY_TABLE',TABLE_PREFIX.'equipment_category');
define('EQUIPMENT_STATUS_TABLE',TABLE_PREFIX.'equipment_status');
define('EQUIPMENT_TICKET_TABLE',TABLE_PREFIX.'equipment_ticket');
define('EQUIPMENT_TICKET_VIEW',TABLE_PREFIX.'EquipmentTicketView');

define('OST_WEB_ROOT', osTicket::get_root_path(__DIR__));

define('OST_ROOT',INCLUDE_DIR.'../');

define('PLUGINS_ROOT',INCLUDE_DIR.'plugins/');

define('EQUIPMENT_PLUGIN_ROOT',__DIR__.'/');
define('EQUIPMENT_INCLUDE_DIR',EQUIPMENT_PLUGIN_ROOT.'include/');
define('EQUIPMENT_APP_DIR',EQUIPMENT_PLUGIN_ROOT.'app/');
define('EQUIPMENT_ASSETS_DIR',EQUIPMENT_PLUGIN_ROOT.'assets/');
define('EQUIPMENT_VENDOR_DIR',EQUIPMENT_PLUGIN_ROOT.'vendor/');
define('EQUIPMENT_VIEWS_DIR',EQUIPMENT_PLUGIN_ROOT.'views/');
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
        Signal::connect('apps.scp', array('EquipmentPlugin', 'callbackDispatch'));
    }
    
    public static function getCustomForm()
    {
        $ep = new EquipmentPlugin();
        $config = $ep->getConfig();
        $form_id = $config->get('equipment_custom_form');
        $form = DynamicForm::lookup($form_id);
        return $form;
    }

    static public function callbackDispatch($object, $data)
    {
        
        $categories_url=url('^/equipment/categories/',patterns(               
                EQUIPMENT_INCLUDE_DIR.'controller/EquipmentCategory.php:EquipmentCategory',
                url_get('^list$', 'listAction'),
                url_get('^listJson$', 'listJsonAction'),
                url_get('^view/(?P<id>\d+)$', 'viewAction'),                
                url_get('^openTicketsJson/(?P<category_id>\d+)$', 'openTicketsJsonAction'),
                url_get('^closedTicketsJson/(?P<category_id>\d+)$', 'closedTicketsJsonAction'),
                url_get('^categoryItemsJson/(?P<category_id>\d+)$', 'categoryItemsJsonAction'),
                url_post('^save', 'saveAction'),
                url_post('^delete', 'deleteAction')
                ));
        
        $item_url=url('^/equipment/item/',patterns(               
                EQUIPMENT_INCLUDE_DIR.'controller/EquipmentItem.php:EquipmentItem',
                url_get('^view/(?P<id>\d+)$', 'viewAction'),   
                url_get('^new/(?P<category_id>\d+)$', 'newAction'),
                url_post('^publish', 'publishAction'),
                url_post('^activate', 'activateAction')
                ));
        
        $status_url=url('^/equipment/status/',patterns(               
                EQUIPMENT_INCLUDE_DIR.'controller/EquipmentStatus.php:EquipmentStatus',
                url_get('^view/(?P<id>\d+)$', 'viewAction'),   
                url_get('^new/(?P<category_id>\d+)$', 'newAction'),
                url_get('^listJson$', 'listJsonAction')
                ));
        
        $media_url=url('^/equipment.*assets/',patterns(               
                EQUIPMENT_INCLUDE_DIR.'controller/MediaController.php:MediaController',                
                url_get('^(?P<url>.*)$', 'defaultAction')))
                ;
        
        $redirect_url=url('^/equipment.*ostroot/',patterns(               
                EQUIPMENT_INCLUDE_DIR.'controller/MediaController.php:MediaController',                
                url_get('^(?P<url>.*)$', 'redirectAction')))
                ;
        
        $object->append($media_url);
        $object->append($redirect_url);
        $object->append($categories_url);
        $object->append($item_url);
        $object->append($status_url);
    }
    /**
     * Creates menu links in the staff backend.
     */
    function createStaffMenu() {

        Application::registerStaffApp('Equipment', 
                'dispatcher.php/equipment/items/view', 
                array(iconclass => 'equipment'));
        Application::registerStaffApp('Equipment Categories', 
                'dispatcher.php/equipment/categories/list', 
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