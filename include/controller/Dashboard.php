<?php
namespace controller;


class Dashboard extends Controller {

    protected function getEntityClassName() {
        return 'model\EquipmentDashboard';
    }

    protected function getListTemplateName() {
        return 'dashboard_list.html.twig';
    }

    protected function getViewTemplateName() {
        return 'dashboard_view.html.twig';
    }
    
     public function displayAction() {
         
     }

    protected function getListColumns() {
        
    }

    protected function getTitle($plural = true) {
        return "Equipment Dashboard";
    }

    protected function getViewDirectory() {
        
    }

}
