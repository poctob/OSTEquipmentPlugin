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

class EquipmentCategory extends Controller {

    protected function getEntityClassName()
    {
        return 'Equipment_Category';
    }
    
    protected function getListTemplateName()
    {
        return 'categories_list.html.twig';
    }
    
    protected function getViewTemplateName()
    {
        return 'categories_view.html.twig';
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
                'asset_id' => $item->getAssetId(),
                'name' => $item->getName(),
                'category' => $item->getCategory()->getName(),
                'status' => $item->getStatus()->getName(),
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
            $ticket = Ticket::lookup($id['ticket_id']);
            $equipment = new Equipment($id['equipment_id']);
            if (isset($ticket) && isset($equipment)) {
                $ticket_data = array(
                    'id' => $ticket->getId(),
                    'number' => $ticket->getNumber(),
                    'equipment' => $equipment->getName(),
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
}
