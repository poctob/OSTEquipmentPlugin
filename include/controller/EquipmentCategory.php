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

    //put your code here

    public function listAction() {
        $categories = Equipment_Category::getAll();
        $this->render('categories_view.html.twig', array(
            'categories' => $categories
        ));
    }

    public function redirectAction($url) {
        header('Content-type: text/javascript');
        include OST_ROOT . 'scp/' . $url;
    }
 
}
