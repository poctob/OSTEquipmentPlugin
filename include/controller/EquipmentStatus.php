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

    public function listAction($errors = array()) {
        $this->render('status_list.html.twig', array(
            'erros' => $errors
        ));
    }

    public function viewAction($id) {
        if ($id > 0) {
            $status = Equipment_Status::lookup($id);
            $title = 'Edit Equipment Status';
        } else {
            $status = new Equipment_Status(0);
            $title = 'New Equipment Status';
        }

        $this->render('status_view.html.twig', array(
            'status' => $status->getHashtable(),
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
        $status = Equipment_Status::getAll();
        echo json_encode($status);
    }

    public function statusItemsJsonAction($status_id) {
        $equipment = Equipment_Status::getEquipment($status_id);
        $items = array();

        foreach ($equipment as $item) {
            $item_data = array(
                'id' => $item->getId(),
                'name' => $item->getName(),
                'status' => $item->getStatus(),
                'published' => $item->isPublished() ? 'Yes' : 'No',
                'active' => $item->isActive() ? 'Yes' : 'No'
            );
            $items[] = $item_data;
        }
        echo json_encode($items);
    }

    public function saveAction() {
        $errors = array();
        Equipment_Status::save($_POST['id'], $_POST, $errors);
        if (isset($errors) && count($errors) > 0) {
            $this->setFlash('error', 'Error Saving Item1', print_r($errors));
        } else {
            $this->setFlash('info', 'Success', 'Item Updated!');
        }

        $this->listAction($errors);
    }

}
