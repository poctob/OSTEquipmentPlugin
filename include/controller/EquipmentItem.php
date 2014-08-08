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

require_once(INCLUDE_DIR . 'class.dynamic_forms.php');

class EquipmentItem extends Controller {

    protected function getEntityClassName() {
        return 'model\Equipment';
    }

    protected function getListColumns() {
        return array(
            array('field' => 'asset_id', 'headerText' => 'Asset ID', 'sortable' => 'true'),
            array('field' => 'category', 'headerText' => 'Category', 'sortable' => 'true'),
            array('field' => 'status', 'headerText' => 'Status', 'sortable' => 'true'),
            array('field' => 'ispublished', 'headerText' => 'Is Published', 'sortable' => 'true'),
            array('field' => 'is_active', 'headerText' => 'Is Active?', 'sortable' => 'true')
        );
    }

    protected function getTitle($plural = true) {
        return $plural ? 'Equipment Items' : 'Equipment Item';
    }

    public function getDynamicForm($id = 0) {
        $form_id = \EquipmentPlugin::getCustomForm();
        if (isset($form_id)) {
            $form = \DynamicForm::lookup($form_id);
            if ($id > 0) {
                $data = \model\Equipment::getDynamicData($id);
                $one = $data->one();
                if (isset($one)) {
                    $one->getSaved();
                    return $one->getForm()->render(true);
                }
            }
            if (isset($form)) {
                return $form->getForm()->render(true);
            }
        }
    }

    public function saveAction() {
        $form_id = \EquipmentPlugin::getCustomForm();
        if (isset($form_id)) {
            \model\Equipment::saveDynamicData($form_id, $_POST['id'],$_POST);            
        }
        return parent::saveAction();
    }

    protected function getViewDirectory() {
        return 'item';
    }
    
    public function publishAction()
    {
        $id = $_POST['item_id'];
        if(isset($id) && $id > 0)
        {
            $item = new \model\Equipment($id);
            if(isset($item))
            {
                $item->setIspublished($_POST['item_publish']);
                $item->save();
            }
        }
    }
    
    public function activateAction()
    {
        $id = $_POST['item_id'];
        if(isset($id) && $id > 0)
        {
            $item = new \model\Equipment($id);
            if(isset($item))
            {
                $item->setIs_active($_POST['item_activate']);
                $item->save();
            }
        }
    }

}
