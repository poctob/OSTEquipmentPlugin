<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of EquipmentCategory
 *
 * @author alex
 */
require_once ('Controller.php');

class Dashboard extends Controller {

    protected function getEntityClassName() {
        return 'Equipment_Dashboard';
    }

    protected function getListTemplateName() {
        return 'dashboard_list.html.twig';
    }

    protected function getViewTemplateName() {
        return 'dashboard_view.html.twig';
    }
    
     public function displayAction() {
         
     }

  
}
