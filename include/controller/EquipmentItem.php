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
        return $plural?'Equipment Items':'Equipment Item';
    }

   /* public function newAction($category_id) {
        $viewargs = array();
        if ($category_id > 0) {
            $category = new model\EquipmentCategory($category_id);
            $viewargs['category'] = $category;
            $this->viewAction(0,
                    $viewargs);
        } else {
            $this->setFlash
                    ('error',
                    'Unable to create new item!',
                    'invalid category specified!');
            $this->viewAction(0);
        }
    }

    protected function defaultAction() {
        $category = new model\EquipmentCategory($_POST['category_id']);
        $viewargs['category'] = $category;
        $this->viewAction($_POST['id'],
                $viewargs);
    }*/

    public function getDynamicForm($id = 0) {
        $form_id = EquipmentPlugin::getCustomForm();
        if (isset($form_id)) {
            $form = DynamicForm::lookup($form_id);
            if ($id > 0) {
                $data = Equipment::getDynamicData($id);
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

    public function publishAction() {
        $id = $_POST['item_id'];
        $result = false;
        if (isset($id)) {
            $equipment = new Equipment($id);
            $publish = $_POST['item_publish'];
            if ($publish) {
                $result = $equipment->publish();
            } else {
                $result = $equipment->unpublish();
            }
        }
        if ($result) {
            $this->setFlash('info',
                    'Success',
                    'Item Updated!');
        } else {
            $this->setFlash('error',
                    'Error',
                    'Failed to Update Item!');
        }
    }

    public function activateAction() {
        $id = $_POST['item_id'];
        $result = false;
        if (isset($id)) {
            $equipment = new Equipment($id);
            $activate = $_POST['item_activate'];
            if ($activate) {
                $result = $equipment->activate();
            } else {
                $result = $equipment->retire();
            }
        }
        if ($result) {
            $this->setFlash('info',
                    'Success',
                    'Item Updated!');
        } else {
            $this->setFlash('error',
                    'Error',
                    'Failed to Update Item!');
        }
    }

    protected function getViewDirectory() {
        return 'item';
    }

}
