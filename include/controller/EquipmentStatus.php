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
require_once(EQUIPMENT_INCLUDE_DIR . 'class.equipment_status.php');

class EquipmentStatus extends Controller {

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

    public function listJsonAction($errors = array()) {
        $status = Equipment_Status::getStatusList();
        echo json_encode($status);
    }

}
