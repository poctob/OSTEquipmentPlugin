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
            array('field' => 'subject', 'headerText' => 'Subject', 'sortable' => 'true'),
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
            $items[] = array('number' => $ticket->getNumber().' - '.$ticket->getSubject(),
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

    protected function getViewDirectory() {
        return 'recurring';
    }

    public function listAction() {
        $enabled = \model\EquipmentConfig::findByKey('recurrance_enabled');
        if (isset($enabled) && $enabled == 'true') {
            parent::listAction();
        } else {
            $args = array();
            $args['title'] = $this->getTitle();
            $args['dt_columns'] = $this->getListColumns();
            $args['enabled'] = $this->checkEventScheduler();

            $template_name = 'listRecurringTemplate.html.twig';
            $this->render($template_name,
                    $args);
        }
    }

    protected function checkEventScheduler() {
        $retval = false;
        $sql = "show variables like '%event_scheduler%'";

        $res = db_query($sql);
        if ($res && ($num = db_num_rows($res))) {
            while ($row = db_fetch_array($res)) {
                $retval = $row['Value'] == 'ON';
            }
        }
        return $retval;
    }

    protected function createEvent() {

        $sql = 'DROP EVENT IF EXISTS `' . TABLE_PREFIX . 'EquipmentCron`';
        db_query($sql);
        $sql = 'CREATE EVENT `' . TABLE_PREFIX . 'EquipmentCron`
                ON SCHEDULE EVERY 1 HOUR
                DO
                CALL `' . TABLE_PREFIX . 'EquipmentCronProc`()';

        $res = db_query($sql);
        if ($res) {
            \model\EquipmentConfig::saveConfig('recurrance_enabled',
                    'true');
        }
        return $res;
    }

    public function enableEventsAction() {
        if ($this->createEvent()) {
            parent::listAction();
            return;
        }
        $args = array();
        $args['title'] = $this->getTitle();
        $template_name = 'listRecurringTemplateFail.html.twig';
        $this->render($template_name,
                $args);
    }

}
