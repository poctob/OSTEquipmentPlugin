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

namespace controller;

class EquipmentCategory extends Controller {

    protected function getEntityClassName() {
        return 'model\EquipmentCategory';
    }

    protected function getViewDirectory() {
        return 'category';
    }

    protected function getTitle($plural = true) {
        return $plural ? 'Equipment Categories' : 'Equipment Category';
    }

    protected function getListColumns() {
        return array(
            array('field' => 'name', 'headerText' => 'Name', 'sortable' => 'true'),
            array('field' => 'ispublic', 'headerText' => 'Type', 'sortable' => 'true'),
            array('field' => 'equipment_count', 'headerText' => 'Equipment', 'sortable' => 'true'),
            array('field' => 'open_ticket_count', 'headerText' => 'Open Tickets',
                'sortable' => 'true'),
            array('field' => 'closed_ticket_count', 'headerText' => 'Closed Tickets',
                'sortable' => 'true'),
            array('field' => 'updated', 'headerText' => 'Last Updated', 'sortable' => 'true')
        );
    }

    public function listJsonTreeAction() {
        $entityClass = $this->getEntityClassName();
        $items = $entityClass::getAll();
        $object = array();
        foreach ($items as $item) {
            if($item->getParent_id() == 0)
            {
                $data = $this->getJsonTreeObject($item);    
                $object[] = $data;
            }
        }

        return json_encode($object);
    }

    private function getJsonTreeObject($item) {
        $data = array();
        $data['label'] = null;
        $data['data'] = $item->getJsonProperties();
        $data['leaf'] = false;
        $children=$item->getChildren();
        $kids=array();
        
        foreach($children as $child)
        {
            $kids[] = $this->getJsonTreeObject($child);
        }
        $data['children'] = $kids;
        return $data;
    }

    public function categoryItemsJsonAction($category_id) {
        $category = new \model\EquipmentCategory($category_id);
        $equipment = $category->getEquipment();
        $items = array();

        foreach ($equipment as $item) {
            $status = $item->getStatus();
            $item_data = array(
                'id' => $item->getId(),
                'asset_id' => $item->getAsset_id(),
                'category' => $category->getName(),
                'status' => isset($status) ? $status->getName() : '',
                'published' => $item->getIspublished() ? 'Yes' : 'No',
                'active' => $item->getIs_active() ? 'Yes' : 'No'
            );
            $items[] = $item_data;
        }
        echo json_encode($items);
    }

    protected function getListTemplateName() {
        return 'listTreeTemplate.html.twig';
    }

}
