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
require_once(EQUIPMENT_INCLUDE_DIR . 'class.equipment.php');

class EquipmentItem extends Controller {

    public function viewAction($id) {
        if ($id > 0) {
            $category = Equipment_Category::lookup($id);
            $title = 'Edit Equipment Catgory';
        } else {
            $category = new Equipment_Category();
            $title = 'New Equipment Catgory';
        }

        $this->render('categories_view.html.twig', array(
            'category' => $category,
            'title' => $title
        ));
    }

    public function newAction($category_id) {
        if ($category_id > 0) {
            $category = Equipment_Category::lookup($category_id);
            $title = 'New Equipment Item';
            $equipment = new Equipment(0);
            $this->render('item_view.html.twig', array(
                'category' => $category,
                'equipment' => $equipment,
                'title' => $title
            ));
        } else {
            echo 'Unable to create new item, invalid category specified!';
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
