<?php

/**
 * Entity class for equipment recurring ticket.
 *
 * @author Alex Pavlunenko 
 */
class Ticket_Recurring {

    private $id;
    private $equipment_id;
    private $ticket_id;
    private $last_opened;
    private $interval;
    private $active;
    private $errors;

    /**
     * Default constructor
     */
    public function Ticket_Recurring($id = 0) {
        $this->id = 0;
        $this->equipment_id = 0;
        $this->ticket_id = 0;
        $this->last_opened = null;
        $this->next_date = null;
        $this->interval = 0;
        $this->active = 0;
        $this->errors = array();

        if ($id > 0) {
            $this->load($id);
        }
    }

    public function delete() {

        $sql = 'DELETE FROM ' . EQUIPMENT_TICKET_RECURRING__TABLE
                . ' WHERE id=' . db_input($this->id)
                . ' LIMIT 1';
        if (db_query($sql) && ($num = db_affected_rows())) {
            return $num;
        }
        $this->errors[] = 'Error deleting item!';
        return false;
    }

    public function save() {
        $retval = false;
        unset($this->errors);
        $this->errors = array();

        if (!$this->validate()) {
            return false;
        }

        $sql = 'equipment_id=' . db_input($this->equipment_id) .
                ',ticket_id=' . db_input($this->ticket_id) .
                ',last_opened=' . db_input($this->last_opened) .
                ',next_date=' . db_input($this->next_date) .
                ',`interval`=' . db_input($this->interval) .
                ',active=' . db_input($this->active);

        if ($this->id > 0) {
            $sql = 'UPDATE ' . EQUIPMENT_TICKET_RECURRING__TABLE .
                    ' SET ' . $sql . ' WHERE id=' . db_input($this->id);
            $retval = db_query($sql);
        } else {
            $sql = 'INSERT INTO ' . EQUIPMENT_TICKET_RECURRING__TABLE .
                    ' SET ' . $sql;
            $retval = db_query($sql);
            if ($retval) {
                $this->id = db_insert_id();
            }
        }
        if (!$retval) {
            $this->errors[] = 'Error saving item!';
        }
        return $retval;
    }

    private function validate() {
        $retval = false;

        $retval = isset($this->equipment_id) && $this->equipment_id > 0;
        if (!$retval) {
            $this->errors[] = 'Invalid Equipment ID!';
            return $retval;
        }

        $retval = isset($this->ticket_id) && $this->ticket_id > 0;
        if (!$retval) {
            $this->errors[] = 'Invalid Ticket ID!';
            return $retval;
        }

        $retval = isset($this->interval) && $this->interval > 0;
        if (!$retval) {
            $this->errors[] = 'Invalid Inverval!';
            return $retval;
        }
        return $retval;
    }

    private function load($id) {
        $sql = ' SELECT * '
                . ' FROM ' . EQUIPMENT_TICKET_RECURRING__TABLE
                . ' WHERE id=' . db_input($id);

        if (!($res = db_query($sql)) || !db_num_rows($res)) {
            $this->errors[] = 'Error loading item!';
            return false;
        }

        $cols = db_fetch_array($res);

        foreach ($cols as $key => $value) {
            $this->$key = $value;
        }

        return true;
    }

    private function reload() {
        return $this->load($this->getId());
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
    
    public function getTicket()
    {
        if($this->ticket_id > 0)
        {
            return Ticket::lookup($this->ticket_id);
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

    public function getErrors() {
        return $this->errors;
    }

    /* End Setters and Getters */
    /*     * ****************************************************************** */

    /*     * ****************************************************************** */
    /* Static Methods */

    public static function getById($id) {
        return new Ticket_Recurring($id);
    }

    public static function getAll() {
        $items = array();
        $sql = 'SELECT id ' .
                ' FROM ' . EQUIPMENT_TICKET_RECURRING__TABLE;

        $res = db_query($sql);
        if ($res && ($num = db_num_rows($res))) {
            while ($row = db_fetch_array($res)) {
                $item = new Ticket_Recurring($row['id']);
                $items[] = $item;
            }
        }

        return $items;
    }
    
    public static function findByEquipmentId($id)
    {
        $items = array();
        $sql = 'SELECT id ' .
                ' FROM ' . EQUIPMENT_TICKET_RECURRING__TABLE .
                ' WHERE equipment_id='.db_input($id);

        $res = db_query($sql);
        if ($res && ($num = db_num_rows($res))) {
            while ($row = db_fetch_array($res)) {
                $item = new Ticket_Recurring($row['id']);
                $items[] = $item;
            }
        }

        return $items;
    }
    
    public static function findByTicketId($id)
    {
        $items = array();
        $sql = 'SELECT id ' .
                ' FROM ' . EQUIPMENT_TICKET_RECURRING__TABLE .
                ' WHERE ticket_id='.db_input($id);

        $res = db_query($sql);
        if ($res && ($num = db_num_rows($res))) {
            while ($row = db_fetch_array($res)) {
                $item = new Ticket_Recurring($row['id']);
                $items[] = $item;
            }
        }

        return $items;
    }

    /* End Static Methods */
    /*     * ****************************************************************** */
}
