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

class EquipmentCategory extends Controller {

    protected function getEntityClassName() {
        return 'Equipment_Category';
    }

    protected function getListTemplateName() {
        return 'categories_list.html.twig';
    }

    protected function getViewTemplateName() {
        return 'categories_view.html.twig';
    }

    public function categoryItemsJsonAction($category_id) {
        $equipment = Equipment_Category::getEquipment($category_id);
        $items = array();

        foreach ($equipment as $item) {
            $item_data = array(
                'id' => $item->getId(),
                'asset_id' => $item->getAssetId(),
                'category' => $item->getCategory()->getName(),
                'status' => $item->getStatus()->getName(),
                'published' => $item->isPublished() ? 'Yes' : 'No',
                'active' => $item->isActive() ? 'Yes' : 'No'
            );
            $items[] = $item_data;
        }
        echo json_encode($items);
    }  
}
