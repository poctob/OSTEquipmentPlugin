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



define('EQUIPMENT_PLUGIN_VERSION', '0.3');

define('EQUIPMENT_TABLE', TABLE_PREFIX . 'equipment');
define('EQUIPMENT_CATEGORY_TABLE', TABLE_PREFIX . 'equipment_category');
define('EQUIPMENT_STATUS_TABLE', TABLE_PREFIX . 'equipment_status');
define('EQUIPMENT_TICKET_TABLE', TABLE_PREFIX . 'equipment_ticket');
define('EQUIPMENT_CONFIG_TABLE', TABLE_PREFIX . 'equipment_config');
define('EQUIPMENT_TICKET_RECURRING__TABLE',
        TABLE_PREFIX . 'equipment_ticket_recurring');
define('EQUIPMENT_TICKET_VIEW', TABLE_PREFIX . 'EquipmentTicketView');

define('OST_WEB_ROOT', osTicket::get_root_path(__DIR__));

define('EQUIPMENT_WEB_ROOT', OST_WEB_ROOT . 'scp/dispatcher.php/equipment/');

define('OST_ROOT', INCLUDE_DIR . '../');

define('PLUGINS_ROOT', INCLUDE_DIR . 'plugins/');

define('EQUIPMENT_PLUGIN_ROOT', __DIR__ . '/');
define('EQUIPMENT_INCLUDE_DIR', EQUIPMENT_PLUGIN_ROOT . 'include/');
define('EQUIPMENT_MODEL_DIR', EQUIPMENT_INCLUDE_DIR . 'model/');
define('EQUIPMENT_CONTROLLER_DIR', EQUIPMENT_INCLUDE_DIR . 'controller/');

define('EQUIPMENT_APP_DIR', EQUIPMENT_PLUGIN_ROOT . 'app/');
define('EQUIPMENT_ASSETS_DIR', EQUIPMENT_PLUGIN_ROOT . 'assets/');
define('EQUIPMENT_VENDOR_DIR', EQUIPMENT_PLUGIN_ROOT . 'vendor/');
define('EQUIPMENT_VIEWS_DIR', EQUIPMENT_PLUGIN_ROOT . 'views/');
define('EQUIPMENT_STAFFINC_DIR', EQUIPMENT_INCLUDE_DIR . 'staff/');
define('EQUIPMENT_CLIENTINC_DIR', EQUIPMENT_INCLUDE_DIR . 'client/');

require_once (EQUIPMENT_VENDOR_DIR . 'autoload.php');
spl_autoload_register(array('EquipmentPlugin', 'autoload'));

class EquipmentPlugin extends Plugin {

    var $config_class = 'EquipmentConfig';

    public static function autoload($className) {
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

        if (file_exists(EQUIPMENT_PLUGIN_ROOT . $fileName)) {
            require $fileName;
        }
    }

    function bootstrap() {
        if ($this->firstRun()) {
            if(!$this->configureFirstRun())
            {
                return false;
            }
        }

        if ($this->needUpgrade()) {
            $this->configureUpgrade();
        }

        $config = $this->getConfig();

        if ($config->get('equipment_backend_enable')) {
            $this->createStaffMenu();
        }
        if ($config->get('equipment_frontend_enable')) {
            $this->createFrontMenu();
        }

        Signal::connect('apps.scp', array('EquipmentPlugin', 'callbackDispatch'));
    }

    public static function getCustomForm() {
        $sql = 'SELECT id FROM ' . PLUGIN_TABLE . ' WHERE name=\'Equipment Manager\'';
        $res = db_query($sql);
        if (isset($res)) {
            $ht = db_fetch_array($res);
            $config = new EquipmentConfig($ht['id']);
            return $config->get('equipment_custom_form');
        }
        return false;
    }

    static public function callbackDispatch($object, $data) {

        $categories_url = url('^/equipment.*categories/',
                patterns(
                        'controller\EquipmentCategory',
                        url_get('^list$', 'listAction'),
                        url_get('^listJson$', 'listJsonAction'),
                        url_get('^listJsonTree$', 'listJsonTreeAction'),
                        url_get('^view/(?P<id>\d+)$', 'viewAction'),
                        url_get('^openTicketsJson/(?P<item_id>\d+)$',
                                'openTicketsJsonAction'),
                        url_get('^closedTicketsJson/(?P<item_id>\d+)$',
                                'closedTicketsJsonAction'),
                        url_get('^getItemsJson/(?P<category_id>\d+)$',
                                'categoryItemsJsonAction'),
                        url_post('^save', 'saveAction'),
                        url_post('^delete', 'deleteAction')
        ));

        $item_url = url('^/equipment.*item/',
                patterns(
                        'controller\EquipmentItem',
                        url_get('^list$', 'listAction'),
                        url_get('^listJson$', 'listJsonAction'),
                        url_get('^view/(?P<id>\d+)$', 'viewAction'),
                        url_get('^new/(?P<category_id>\d+)$', 'newAction'),
                        url_post('^publish', 'publishAction'),
                        url_post('^activate', 'activateAction'),
                        url_post('^save', 'saveAction'),
                        url_get('^openTicketsJson/(?P<item_id>\d+)$',
                                'openTicketsJsonAction'),
                        url_get('^closedTicketsJson/(?P<item_id>\d+)$',
                                'closedTicketsJsonAction'),
                        url_get('^getDynamicForm/(?P<id>\d+)$', 'getDynamicForm'),
                        url_post('^delete', 'deleteAction')
        ));

        $status_url = url('^/equipment.*status/',
                patterns(
                        'controller\EquipmentStatus',
                        url_get('^list$', 'listAction'),
                        url_get('^view/(?P<id>\d+)$', 'viewAction'),
                        url_get('^new/(?P<category_id>\d+)$', 'newAction'),
                        url_get('^listJson$', 'listJsonAction'),
                        url_get('^getItemsJson/(?P<status_id>\d+)$',
                                'statusItemsJsonAction'),
                        url_post('^save', 'saveAction'),
                        url_post('^delete', 'deleteAction')
        ));

        $recurring_url = url('^/equipment.*recurring/',
                patterns(
                        'controller\TicketRecurring',
                        url_get('^list$', 'listAction'),
                        url_get('^view/(?P<id>\d+)$', 'viewAction'),
                        url_get('^viewByTicket/(?P<id>\d+)$',
                                'viewByTicketAction'),
                        url_get('^addByTicket/(?P<id>\d+)$', 'addByTicketAction'),
                        url_get('^new/(?P<category_id>\d+)$', 'newAction'),
                        url_get('^listJson$', 'listJsonAction'),
                        url_get('^getItemsJson/(?P<status_id>\d+)$',
                                'statusItemsJsonAction'),
                        url_get('^listTicketsJson$', 'listTicketsJson'),
                        url_get('^listEquipmentJson$', 'listEquipmentJson'),
                        url_post('^save', 'saveAction'),
                        url_post('^delete', 'deleteAction'),
                        url_post('^enableEvents', 'enableEventsAction')
        ));

        $media_url = url('^/equipment.*assets/',
                patterns(
                        'controller\MediaController',
                        url_get('^(?P<url>.*)$', 'defaultAction')))
        ;

        $dashboard_url = url('^/equipment.*dashboard/',
                patterns(
                        'controller\Dashboard', url_get('.*', 'viewAction')))
        ;

        $redirect_url = url('^/equipment.*ostroot/',
                patterns(
                        'controller\MediaController',
                        url_get('^(?P<url>.*)$', 'redirectAction')))
        ;

        $object->append($media_url);
        $object->append($redirect_url);
        $object->append($dashboard_url);
        $object->append($categories_url);
        $object->append($item_url);
        $object->append($status_url);
        $object->append($recurring_url);
    }

    /**
     * Creates menu links in the staff backend.
     */
    function createStaffMenu() {

        Application::registerStaffApp('Equipment',
                'dispatcher.php/equipment/dashboard/',
                array(iconclass => 'faq-categories'));
    }

    /**
     * Creates menu link in the client frontend.
     * Useless as of OSTicket version 1.9.2.
     */
    function createFrontMenu() {
        Application::registerClientApp('Equipment Status',
                'equipment_front/index.php', array(iconclass => 'equipment'));
    }

    /**
     * Checks if this is the first run of our plugin.
     * @return boolean
     */
    function firstRun() {
        $sql = 'SHOW TABLES LIKE \'' . EQUIPMENT_TABLE . '\'';
        $res = db_query($sql);
        return (db_num_rows($res) == 0);
    }

    function needUpgrade() {
        $sql = 'SELECT version FROM ' . PLUGIN_TABLE . ' WHERE name=\'Equipment Manager\'';

        if (!($res = db_query($sql))) {
            return true;
        } else {
            $ht = db_fetch_array($res);
            if (floatval($ht['version']) < floatval(EQUIPMENT_PLUGIN_VERSION)) {
                return true;
            }
        }
        return false;
    }

    function configureUpgrade() {

        $installer = new \util\EquipmentInstaller();
        if (!$installer->upgrade()) {
            echo "Upgrade configuration error.  "
            . "Unable to upgrade database tables!";
        }
    }

    /**
     * Necessary functionality to configure first run of the application
     */
    function configureFirstRun() {
        if (!$this->createDBTables()) {
            echo "First run configuration error.  "
            . "Unable to create database tables!";
            return false;
        }
        return true;
    }

    /**
     * Kicks off database installation scripts
     * @return boolean
     */
    function createDBTables() {
        $installer = new \util\EquipmentInstaller();
        return $installer->install();
    }

    /**
     * Uninstall hook.
     * @param type $errors
     * @return boolean 
     */
    function pre_uninstall(&$errors) {
        $installer = new \util\EquipmentInstaller();
        return $installer->remove();
    }

}
