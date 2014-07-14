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
require_once(EQUIPMENT_INCLUDE_DIR . 'class.equipment_category.php');


class EquipmentCategory extends Controller {

    public function listAction($errors = array()) {
      
        $this->render('categories_list.html.twig', array(
            'erros' => $errors
        ));
    }
    
    public function listJsonAction($errors = array()) {
        $categories = Equipment_Category::getAll();
        echo json_encode($categories);
    }


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

    public function redirectAction($url) {
        header('Content-type: text/javascript');
        include OST_ROOT . 'scp/' . $url;
    }
    
    public function saveAction() {
        $errors = array();
        Equipment_Category::save($_POST['id'], $_POST, $errors);
        $this->listAction($errors);        
    }

}
