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

    public function openTicketsJsonAction($category_id) {
        $tickets = $this->ticketsAction('open', $category_id);
        echo json_encode($tickets);
    }

    public function closedTicketsJsonAction($category_id) {
        $tickets = $this->ticketsAction('closed', $category_id);
        echo json_encode($tickets);
    }

    public function categoryItemsJsonAction($category_id) {
        $equipment = Equipment_Category::getEquipment($category_id);
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

    private function ticketsAction($type, $category_id) {
        $ticket_id = Equipment_Category::getTicketList($type, $category_id);
        $tickets = array();
        foreach ($ticket_id as $id) {
            $ticket = Ticket::lookup($id);
            if (isset($ticket)) {
                $ticket_data = array(
                    'id' => $ticket->getId(),
                    'number' => $ticket->getNumber(),
                    'create_date' => Format::db_datetime($ticket->getCreateDate()),
                    'subject' => $ticket->getSubject(),
                    'name' => $ticket->getName()->getFull(),
                    'priority' => $ticket->getPriority(),
                );

                if ($type == 'closed') {
                    $ts_open = strtotime($ticket->getCreateDate());
                    $ts_closed = strtotime($ticket->getCloseDate());
                    $ticket_data['close_date'] = Format::db_datetime($ticket->getCloseDate());
                    $ticket_data['closed_by'] = $ticket->getStaff()->getUserName();
                    $ticket_data['elapsed'] = Format::elapsedTime($ts_closed - $ts_open);
                }

                $tickets[] = $ticket_data;
            }
        }
        return $tickets;
    }

    public function viewAction($id) {
        if ($id > 0) {
            $category = Equipment_Category::lookup($id);
            $title = 'Edit Equipment Category';
        } else {
            $category = new Equipment_Category();
            $title = 'New Equipment Category';
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

    public function deleteAction() {
        $category = new Equipment_Category($_POST['category_id']);
        if (isset($category)) {
            $category->delete();
        }
    }

}
