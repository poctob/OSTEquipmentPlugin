<?php

namespace model;

/**
 * Entity class for equipment recurring ticket.
 *
 * @author Alex Pavlunenko 
 */
class TicketRecurring extends Entity {

    private $id;
    private $equipment_id;
    private $ticket_id;
    private $last_opened;
    private $interval;
    private $active;

    public function getJsonProperties() {
        return array(
            'id' => $this->getId(),
            'equipment' => $this->getEquipment()->getAsset_id(),
            'ticket' => $this->getTicket()->getNumber(),
            'last_opened' => $this->getLast_opened(),
            'interval' => $this->getInterval(),
            'active' => $this->getActive()?'Yes':'No',
        );
    }

    protected function validate() {
        $retval = isset($this->equipment_id) && $this->equipment_id > 0;
        if (!$retval) {
            $this->addError('Invalid Equipment ID!');
            return $retval;
        }

        $retval = isset($this->ticket_id) && $this->ticket_id > 0;
        if (!$retval) {
            $this->addError('Invalid Ticket ID!');
            return $retval;
        }

        $retval = isset($this->interval) && $this->interval > 0;
        if (!$retval) {
            $this->addError('Invalid Inverval!');
            return $retval;
        }
        return $retval;
    }

    /*     * ****************************************************************** */
    /* Setters and Getters */

    public function getId() {
        return $this->id;
    }

    public function getEquipment_id() {
        return $this->equipment_id;
    }

    public function getEquipment() {
        if ($this->equipment_id > 0) {
            return new Equipment($this->equipment_id);
        }
        return null;
    }

    public function getTicket_id() {
        return $this->ticket_id;
    }

    public function getTicket() {
        if ($this->ticket_id > 0) {
            return \Ticket::lookup($this->ticket_id);
        }
        return null;
    }

    public function getLast_opened() {
        return $this->last_opened;
    }

    public function getNext_date() {
        return $this->next_date;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setEquipment_id($equipment_id) {
        $this->equipment_id = $equipment_id;
    }

    public function setTicket_id($ticket_id) {
        $this->ticket_id = $ticket_id;
    }

    public function setLast_opened($last_opened) {
        $this->last_opened = $last_opened;
    }

    public function setNext_date($next_date) {
        $this->next_date = $next_date;
    }

    public function getInterval() {
        return $this->interval;
    }

    public function setInterval($interval) {
        $this->interval = $interval;
    }

    public function getActive() {
        return $this->active;
    }

    public function setActive($active) {
        $this->active = $active;
    }

    /* End Setters and Getters */
    /*     * ****************************************************************** */

    /*     * ****************************************************************** */
    /* Static Methods */

    public static function findByEquipmentId($id) {
        $items = array();
        $sql = 'SELECT id ' .
                ' FROM ' . EQUIPMENT_TICKET_RECURRING__TABLE .
                ' WHERE equipment_id=' . db_input($id);

        return self::populateBySQL($sql);
    }

    public static function findByTicketId($id) {
        $items = array();
        $sql = 'SELECT id ' .
                ' FROM ' . EQUIPMENT_TICKET_RECURRING__TABLE .
                ' WHERE ticket_id=' . db_input($id);

        return self::populateBySQL($sql);
    }

    protected function getSaveSQL() {
        $sql = 'equipment_id=' . db_input($this->equipment_id) .
                ',ticket_id=' . db_input($this->ticket_id) .
                ',last_opened=' . db_input($this->last_opened) .
                ',next_date=' . db_input($this->next_date) .
                ',`interval`=' . db_input($this->interval) .
                ',active=' . db_input($this->active);
        return $sql;
    }

    protected function init() {
        $this->id = 0;
        $this->equipment_id = 0;
        $this->ticket_id = 0;
        $this->last_opened = null;
        $this->next_date = null;
        $this->interval = 0;
        $this->active = 0;
    }

    protected static function getIdColumn() {
        return 'id';
    }

    protected static function getTableName() {
        return EQUIPMENT_TICKET_RECURRING__TABLE;
    }

    /* End Static Methods */
    /*     * ****************************************************************** */
}
