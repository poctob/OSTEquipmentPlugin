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

class TicketRecurring extends Controller {

    protected function getEntityClassName() {
        return 'model\TicketRecurring';
    }

    protected function getListColumns() {
        return array(
            array('field' => 'equipment', 'headerText' => 'Equipment', 'sortable' => 'true'),
            array('field' => 'ticket', 'headerText' => 'Ticket', 'sortable' => 'true'),
            array('field' => 'last_opened', 'headerText' => 'Last Ocurrence', 'sortable' => 'true'),
            array('field' => 'hr_interval', 'headerText' => 'Interval', 'sortable' => 'true'),
            array('field' => 'active', 'headerText' => 'Is Active?', 'sortable' => 'true')
        );
    }

    protected function getTitle($plural = true) {
        return $plural ? 'Recurring Tickets' : 'Recurring Ticket';
    }

    public function listTicketsJson() {
        $tickets = \model\EquipmentTicket::getAllTickets();
        $items = array();
        foreach ($tickets as $ticket) {
            $items[] = array('number' => $ticket->getNumber(),
                'ticket_id' => $ticket->getId());
        }

        return json_encode($items);
    }

    public function listEquipmentJson() {
        $equipments = \model\EquipmentTicket::getAllEquipment();
        $items = array();
        foreach ($equipments as $equipment) {
            $items[] = array('asset_id' => $equipment->getAsset_Id(),
                'equipment_id' => $equipment->getId());
        }

        return json_encode($items);
    }

    public function viewByTicketAction($id = 0) {
        $args = array();

        if (isset($id) && $id > 0) {
            $ticket = \Ticket::lookup($id);
            if (isset($ticket)) {
                $entityClass = $this->getEntityClassName();
                $items = $entityClass::findByTicketId($id);
                $args['ticket'] = $ticket;
                $args['items'] = $items;
            }
        }

        $this->viewAction(-1,
                $args);
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
        $this->render($template_name,
                $args);
    }

    /*   public function saveAction($id = 0) {
      $class = $this->getEntityClassName();
      $item = new $class($id);
      if (isset($item)) {
      $item->setTicket_id($_POST['ticket_id']);
      $item->setEquipment_id($_POST['equipment_id']);
      $date = strtotime($_POST['next_date']);
      $db_date = date('Y-m-d H:i:s',
      $date);
      $item->setNext_date($db_date);

      $active = $_POST['active'] == 'on' ? 1 : 0;
      $item->setActive($active);

      $interval = $_POST['interval'];
      $multiplier = $_POST['interval_multiplier'];

      $item->setInterval(intval($interval) * intval($multiplier));

      if ($item->save()) {
      $this::setFlash('info',
      'Success!',
      'Item Saved');
      } else {
      $this::setFlash('error',
      'Failed to save item!',
      print_r($item->getErrors()));
      }
      }
      $this->defaultAction();
      } */

    protected function getViewDirectory() {
        return 'recurring';
    }

}
