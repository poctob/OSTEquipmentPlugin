<?php

namespace controller;

class Maintenance {

    private $progress = 0;

    public function defaultAction() {
        $loader = new \Twig_Loader_Filesystem(EQUIPMENT_VIEWS_DIR);
        $twig = new \Twig_Environment($loader);
        $args = array();
        $args['title'] = 'Plugin Maintenance';
        global $ost;
        $staff = \StaffAuthenticationBackend::getUser();
        $tocken = $ost->getCSRF();

        $args['staff'] = $staff;
        $args['linktoken'] = $ost->getLinkToken();
        $args['tocken'] = $tocken->getToken();
        $args['tocken_name'] = $tocken->getTokenName();

        echo $twig->render('maintenanceTemplate.html.twig',
                $args);
    }

    public function startDatabaseIntegrityTest() {       
       $returnData = 'Test results: <br />';
       $returnData = $this->needUpgrade();
       $returnData.=$this->checkTables();
       $returnData.= '<br />';
       $returnData.=$this->checkListTableData();

       return $returnData;
    }
    
    public function startDatabaseDataPurge()
    {
        $installer = new \util\EquipmentInstaller();
        if($installer->purgeData())
        {
            return '<p style="display:inline; color: green;">Data purged!</p><br />'; 
        }
        else
        {
             return '<p style="display:inline; color: red;">Failed to purge data!</p><br />'; 
        }
    }
    
    public function startDatabaseRecreate()
    {
        $installer = new \util\EquipmentInstaller();
        if($installer->remove() && $installer->install())
        {
            return '<p style="display:inline; color: green;">Database recreated!</p><br />'; 
        }
        else
        {
             return '<p style="display:inline; color: red;">Failed to recreate database!</p><br />'; 
        }
    }
    
    private function checkTables()
    {       
        $retval = $this->checkTableExists(EQUIPMENT_TABLE);
        $retval.= $this->checkTableExists(EQUIPMENT_CATEGORY_TABLE);
        $retval.= $this->checkTableExists(EQUIPMENT_STATUS_TABLE);
        $retval.= $this->checkTableExists(EQUIPMENT_TICKET_TABLE);
        $retval.= $this->checkTableExists(EQUIPMENT_TICKET_RECURRING__TABLE);
        $retval.= $this->checkTableExists(EQUIPMENT_CONFIG_TABLE);
        $retval.= '<br />';
        $retval.= $this->checkTableExists(EQUIPMENT_TICKET_VIEW, true);
        $retval.= $this->checkTableExists(EQUIPMENT_SEARCH_VIEW, true);
        $retval.= $this->checkTableExists(EQUIPMENT_FORM_VIEW, true);
        $retval.= '<br />';
        $retval.= $this->checkTriggerExists(EQUIPMENT_DELETE_TRIGGER);
        $retval.= $this->checkTriggerExists(EQUIPMENT_INSERT_TRIGGER);
        $retval.= $this->checkTriggerExists(EQUIPMENT_UPDATE_TRIGGER);
        $retval.= $this->checkTriggerExists(STATUS_INSERT_TRIGGER);
        $retval.= $this->checkTriggerExists(STATUS_UPDATE_TRIGGER);
        $retval.= $this->checkTriggerExists(STATUS_DELETE_TRIGGER);
        $retval.= $this->checkTriggerExists(EVENT_DELETE_TRIGGER);
        $retval.= $this->checkTriggerExists(EVENT_UPDATE_TRIGGER);
        $retval.= '<br />';
        $retval.= $this->checkProcedureExists(CREATE_FORM_FIELDS_PROCEEDURE);        
        $retval.= $this->checkProcedureExists(COPY_FORM_ENTRY_PROCEEDURE);   
        $retval.= $this->checkProcedureExists(REOPEN_TICKET_PROCEEDURE);  
        $retval.= $this->checkProcedureExists(CRON_PROCEEDURE);  
        
        return $retval;        
    }
    
    private function checkTableExists($table, $isview = false)
    {
        $sql = 'SHOW TABLES LIKE \'' . $table . '\'';
        $res = db_query($sql);
        $retval.= ($isview?'View ':'Table ').$table;
        if(db_num_rows($res) != 0)
        {
            $retval.= ' found: <p style="display:inline; color: green;">Pass</p><br />'; 
        }
        else
        {
            $retval.= ' not found: <p style="display:inline; color: red;">Fail</p><br />';
        }
        
        return $retval;
    }
    
    private function checkTriggerExists($trigger)
    {
        $sql = 'SHOW TRIGGERS WHERE `Trigger` LIKE \'' . $trigger . '\'';
        $res = db_query($sql);
        $retval.= 'Trigger '.$trigger;
        if(db_num_rows($res) != 0)
        {
            $retval.= ' found: <p style="display:inline; color: green;">Pass</p><br />'; 
        }
        else
        {
            $retval.= ' not found: <p style="display:inline; color: red;">Fail</p><br />';
        }
        
        return $retval;
    }
    
    private function checkProcedureExists($procedure)
    {
        $sql = 'SHOW PROCEDURE STATUS WHERE `Db`=\''.DBNAME.'\' AND `Name` like \''.$procedure.'\'';               
        $res = db_query($sql);
        $retval.= 'Procedure '.$procedure;
        if(db_num_rows($res) != 0)
        {
            $retval.= ' found: <p style="display:inline; color: green;">Pass</p><br />'; 
        }
        else
        {
            $retval.= ' not found: <p style="display:inline; color: red;">Fail</p><br />';
        }
        
        return $retval;
    }
    
    private function needUpgrade() {
        $sql = 'SELECT version FROM ' . PLUGIN_TABLE . ' WHERE name=\'Equipment Manager\'';

        if (!($res = db_query($sql))) {
            return '<p style="color: red;">'
                . 'Error! No version information found! <br /></p>';
        } else {
            $ht = db_fetch_array($res);
            if (floatval($ht['version']) < floatval(EQUIPMENT_PLUGIN_VERSION)) {
                return '<p style="color: red;">'
                . 'Plugin version is does not match database version: <br />'
                        .'Expected '.EQUIPMENT_PLUGIN_VERSION.' <br />'
                        . 'Actual'.floatval($ht['version']).'</p>';
            }
        }
        return '<p style="color: green;">Plugin and Database versions match.</p>';
    }
    
    private function checkListTableData()
    {
        $sql = 'SELECT * FROM '. LIST_TABLE.' WHERE `name`=\'equipment_status\'';                
        $res = db_query($sql);
        $retval.= 'Table '. LIST_TABLE;
        if(db_num_rows($res) == 0)
        {
            $retval.= ' missing Equipment Status list: <p style="display:inline; color: red;">Fail</p><br />';
        }
        else if(db_num_rows($res) >1 )
        {
            $retval.= ' contains a duplicate Equipment Status list: <p style="display:inline; color: red;">Fail</p><br />';
        }
        else if(db_num_rows($res) == 1 )
        {
            $retval.= ' found Equipment Status list: <p style="display:inline; color: green;">Pass</p><br />';
        }
        
        $sql = 'SELECT * FROM '. LIST_TABLE.' WHERE `name`=\'equipment\'';                
        $res = db_query($sql);
        $retval.= 'Table '. LIST_TABLE;
        if(db_num_rows($res) == 0)
        {
            $retval.= ' missing Equipment list: <p style="display:inline; color: red;">Fail</p><br />';
        }
        else if(db_num_rows($res) >1 )
        {
            $retval.= ' contains a duplicate Equipment list: <p style="display:inline; color: red;">Fail</p><br />';
        }
        else if(db_num_rows($res) == 1 )
        {
            $retval.= ' found Equipment list: <p style="display:inline; color: green;">Pass</p><br />';
        }
        
         $sql = 'SELECT * FROM '. FORM_SEC_TABLE.' WHERE `title`=\'Equipment\'';                
        $res = db_query($sql);
        $retval.= 'Table '. FORM_SEC_TABLE;
        if(db_num_rows($res) == 0)
        {
            $retval.= ' missing Equipment form: <p style="display:inline; color: red;">Fail</p><br />';
        }
        else if(db_num_rows($res) >1 )
        {
            $retval.= ' contains a duplicate Equipment form: <p style="display:inline; color: red;">Fail</p><br />';
        }
        else if(db_num_rows($res) == 1 )
        {
            $retval.= ' found Equipment form: <p style="display:inline; color: green;">Pass</p><br />';
        }
        
        return $retval;
    }
    

  

}

