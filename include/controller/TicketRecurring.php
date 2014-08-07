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

class TicketRecurring extends Controller {

    protected function getEntityClassName() {
        return 'Ticket_Recurring';
    }

    protected function getListTemplateName() {
        return 'recurring_list.html.twig';
    }

    protected function getViewTemplateName() {
        return 'recurring_view.html.twig';
    }

    protected function getAddTemplateName() {
        return 'recurring_add.html.twig';
    }

    public function viewByTicketAction($id = 0) {
        $args = array();

        if (isset($id) && $id > 0) {
            $ticket = Ticket::lookup($id);
            if (isset($ticket)) {
                $entityClass = $this->getEntityClassName();
                $items = $entityClass::findByTicketId($id);
                $title = 'Reccuring Ticket Information';
                $args['ticket'] = $ticket;
                $args['items'] = $items;
                $args['title'] = $title;
            }
        }

        $template_name = $this->getViewTemplateName();
        $this->render($template_name, $args);
    }

    public function addByTicketAction($id = 0) {
        $args = array();
        if (isset($id) && $id > 0) {
            $ticket = Ticket::lookup($id);
        }
        $ticket_items = Equipment_Ticket::getAllTickets();
        $tickets = array();
        foreach ($ticket_items as $item) {
            $ti = array('id' => $item->getId(),
                'number' => $item->getNumber(),
                'subject' => $item->getSubject());
            $tickets[] = $ti;
        }

        $equipment_items = Equipment_Ticket::getAllEquipment();
        $equipments = array();
        foreach ($equipment_items as $item) {
            $ti = array('id' => $item->id,
                'asset_id' => $item->asset_id);
            $equipments[] = $ti;
        }

        $title = 'New Recurring Ticket';
        $args['ticket'] = $ticket;
        $args['tickets'] = $tickets;
        $args['equipments'] = $equipments;
        $args['title'] = $title;

        $template_name = $this->getAddTemplateName();
        $this->render($template_name, $args);
    }
    
    public function saveAction($id = 0) {
        $class = $this->getEntityClassName();
        $item = new $class($id);
        if(isset($item))
        {
            $item->setTicket_id($_POST['ticket_id']);
            $item->setEquipment_id($_POST['equipment_id']);
            $date = strtotime($_POST['next_date']);
            $db_date = date( 'Y-m-d H:i:s', $date );
            $item->setNext_date($db_date);
            
            $active=$_POST['active']=='on'?1:0;
            $item->setActive($active);
            
            $interval = $_POST['interval'];
            $multiplier = $_POST['interval_multiplier'];
            
            $item->setInterval(intval($interval)*intval($multiplier));
            
            if($item->save())
            {
                $this::setFlash('info', 'Success!', 'Item Saved');
            }
            else
            {
                 $this::setFlash('error', 'Failed to save item!', 
                         print_r($item->getErrors()));
            }
        }
        $this->defaultAction();
    }

}
