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

class EquipmentStatus extends Controller {

    protected function getEntityClassName() {
        return 'Equipment_Status';
    }

    protected function getListTemplateName() {
        return 'status_list.html.twig';
    }

    protected function getViewTemplateName() {
        return 'status_view.html.twig';
    }

    public function statusItemsJsonAction($status_id) {
        $equipment = Equipment_Status::getEquipment($status_id);
        $items = array();

        foreach ($equipment as $item) {
            if ($item->getId() > 0) {
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
        }
        echo json_encode($items);
    }

}
