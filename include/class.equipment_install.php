<?php
/*********************************************************************
    class.equipment_install.php

    Equipment extension Intaller - installs the latest version.

    Copyright (c)  2006-2013 XpressTek
    http://www.xpresstek.net

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
**********************************************************************/
require_once 'class.setup.php';

class EquipmentInstaller extends SetupWizard {

    /**
     * Loads, checks and installs SQL file.
     * @return boolean
     */
    function install() {
            
         $schemaFile =EQUIPMENT_PLUGIN_ROOT.'install/sql/install_equipment.sql'; //DB dump.	
        
        //Last minute checks.
        if(!file_exists($schemaFile))
	{
	    echo '<br />';
	    var_dump($schemaFile);
	    echo '<br />';    
            echo 'File Access Error - please make sure your download is the latest (#1)';  
            echo '<br />'; 
	    $this->error='File Access Error!';   
            return false;
	    
	}	
        elseif(!$this->load_sql_file($schemaFile,TABLE_PREFIX, true, true))
	{
            echo '<br />';
            echo 'Error parsing SQL schema! Get help from developers (#4)';          
	    echo '<br />';       
            return false;
	}

      return true;
    }
}
?>