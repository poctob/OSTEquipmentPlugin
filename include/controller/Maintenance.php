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
       $returnData.=$this->checkTables();
       return $returnData;
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
            $retval.= ' not found: <p style="display:inline; color: red;">Red</p><br />';
        }
        
        return $retval;
    }

  

}

