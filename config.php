<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of config
 *
 * @author Alex Pavlunenko <alexp at xpresstek.net>
 */

require_once(INCLUDE_DIR.'/class.plugin.php');
require_once(INCLUDE_DIR.'/class.forms.php');

class EquipmentConfig extends PluginConfig{
    function getOptions() {
        return array(
          //  'title' => 'Equipment Options',
            'equipment_backend_enable' => new BooleanField(array(
                'id'    => 'equipment_backend_enable',
                'label' => 'Enable Backend',
                'configuration' => array(
                    'desc' => 'Staff backend interface')                
            )),
            'equipment_frontend_enable' => new BooleanField(array(
                'id'    => 'equipment_frontend_enable',
                'label' => 'Enable Frontend',
                 'configuration' => array(
                    'desc' => 'Client facing interface')  
            )),
                       
    );
    }
    
      function pre_save(&$config, &$errors) {
        global $msg;

        if (!$errors)
            $msg = 'Configuration updated successfully';

        return true;
    }
}
?>