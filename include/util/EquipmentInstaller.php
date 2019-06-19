<?php
namespace util;

use Phinx\Config\Config as PhinxConfig;
use Phinx\Migration\Manager as PhinxManager;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;

/*
 * ******************************************************************* class.equipment_install.php Equipment extension Intaller - installs the latest version. Copyright (c) 2006-2013 XpressTek http://www.xpresstek.net Released under the GNU General Public License WITHOUT ANY WARRANTY. See LICENSE.TXT for details. vim: expandtab sw=4 ts=4 sts=4: ********************************************************************
 */
require_once 'class.setup.php';
require_once INCLUDE_DIR . 'class.staff.php';

class EquipmentInstaller extends \SetupWizard
{

    private $migrator;
    private $config;

    public function __construct()
    {
        print_r($sql);
        $configArray = require EQUIPMENT_PLUGIN_ROOT . 'phinx.php';
        $configArray['paths']['migrations'] = EQUIPMENT_PLUGIN_ROOT . 'db/migrations';
        $configArray['environments']['default_migration_table'] = TABLE_PREFIX . 'phinxlog';
        $configArray['environments']['development'] = [
            'adapter' => 'mysql',
            'host' => DBHOST,
            'name' => DBNAME,
            'user' => DBUSER,
            'pass' => DBPASS,
            'port' => '3306',
            'charset' => 'utf8',
            'table_prefix' => TABLE_PREFIX,
        ];

        $this->config = new PhinxConfig($configArray);
    }

    public function getDBMigrationsStatus()
    {
        $buffer = new BufferedOutput();
        $manager = new PhinxManager($this->config, new StringInput(' '), $buffer);
        $manager->printStatus('development');

        return $buffer->fetch();
    }
    /**
     * Loads, checks and installs SQL file.
     *
     * @return boolean
     */
    public function install()
    {
        return true;
        // $schemaFile = EQUIPMENT_PLUGIN_ROOT . 'install/sql/install_equipment.sql'; // DB dump.
        // return $this->runJob($schemaFile);
    }
    public function upgrade()
    {
        // $schemaFile = EQUIPMENT_PLUGIN_ROOT . 'install/sql/upgrade_equipment_fail.sql'; // DB dump.
        // $this->runJob($schemaFile, false);
        // // $schemaFile = EQUIPMENT_PLUGIN_ROOT . 'install/sql/upgrade_equipment.sql'; // DB dump.
        // return $this->runJob($schemaFile);
    }

    public function remove()
    {
        // $schemaFile = EQUIPMENT_PLUGIN_ROOT . 'install/sql/remove_equipment.sql'; // DB dump.
        // return $this->runJob($schemaFile);
    }
    public function purgeData()
    {
        // $schemaFile = EQUIPMENT_PLUGIN_ROOT . 'install/sql/purge_equipment_data.sql'; // DB dump.
        // return $this->runJob($schemaFile);
    }
}
