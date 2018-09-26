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

class EquipmentStatus extends Controller
{

    protected function getEntityClassName()
    {
        return '\EquipmentDB\EquipmentDB\OstEquipmentStatus';
    }

    public function statusItemsJsonAction($status_id)
    {
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
                    'active' => $item->isActive() ? 'Yes' : 'No',
                );
                $items[] = $item_data;
            }
        }
        echo json_encode($items);
    }

    protected function getListColumns()
    {
        return array(
            array('field' => 'name', 'headerText' => 'Name', 'sortable' => 'true'),
            array('field' => 'color', 'headerText' => 'Color', 'sortable' => 'true'),
            array('field' => 'image', 'headerText' => 'Image', 'sortable' => 'true'),
            array('field' => 'equipments', 'headerText' => 'Equipment', 'sortable' => 'true'),
            array('field' => 'baseline', 'headerText' => 'Is Default?', 'sortable' => 'true'),
        );
    }

    protected function getTitle($plural = true)
    {
        return 'Equipment Status';
    }

    protected function getViewDirectory()
    {
        return 'status';
    }

}
