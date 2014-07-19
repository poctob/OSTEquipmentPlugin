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

class EquipmentItem extends Controller {

    protected function getEntityClassName() {
        return 'Equipment';
    }

    protected function getListTemplateName() {
        return 'categories_list.html.twig';
    }

    protected function getViewTemplateName() {
        return 'item_view.html.twig';
    }

    protected function defaultAction() {
        $this->newAction($_POST['category_id']);
    }

    public function newAction($category_id) {
        $viewargs = array();
        if ($category_id > 0) {
            $category = Equipment_Category::lookup($category_id);
            $viewargs['category'] = $category;
            $this->viewAction(0, $viewargs);
        } else {
            $this->setFlash
                    ('error', 'Unable to create new item!', 'invalid category specified!');
            $this->viewAction(0);
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
            $this->setFlash('info', 'Success', 'Item Updated!');
        } else {
            $this->setFlash('error', 'Error', 'Failed to Update Item!');
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
            $this->setFlash('info', 'Success', 'Item Updated!');
        } else {
            $this->setFlash('error', 'Error', 'Failed to Update Item!');
        }
    }

}
