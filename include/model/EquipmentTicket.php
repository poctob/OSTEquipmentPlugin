<?php
namespace model;
/**
 * Entity class for equipment recurring ticket.
 *
 * @author Alex Pavlunenko 
 */
class Equipment_Ticket {

    private $equipment_id;
    private $ticket_id;
    private $created;

    /**
     * Default constructor
     */
    public function Equipment_Ticket($t_id = 0, $e_id = 0) {
        $this->equipment_id = 0;
        $this->ticket_id = 0;
        $this->created = null;



        if ($t_id > 0 && $e_id > 0) {
            $this->load($t_id, $e_id);
        }
    }

    public function delete() {

        $sql = 'DELETE FROM ' . EQUIPMENT_TICKET_TABLE
                . ' WHERE equipment_id=' . db_input($this->equipment_id)
                . ' AND ticket_id=' . db_input($this->ticket_id)
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
                ',$created=' . db_input($this->$created);

        if ($this->ticket_id > 0 && $this->equipment_id) {
            $sql = 'UPDATE '
                    . EQUIPMENT_TICKET_TABLE . ' SET ' . $sql
                    . ' WHERE equipment_id=' . db_input($this->equipment_id)
                    . ' AND ticket_id=' . db_input($this->ticket_id);
            $retval = db_query($sql);
        } else {
            $sql = 'INSERT INTO ' . EQUIPMENT_TICKET_TABLE .
                    ' SET ' . $sql;
            $retval = db_query($sql);
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
        return $retval;
    }

    private function load($t_id, $e_id) {
        $sql = ' SELECT * '
                . ' FROM ' . EQUIPMENT_TICKET_TABLE
                . ' WHERE ticket_id=' . db_input($t_id)
                . ' AND equipment_id=' . db_input($e_id);

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

    public function getCreated() {
        return $this->created;
    }

    public function setEquipment_id($equipment_id) {
        $this->equipment_id = $equipment_id;
    }

    public function setTicket_id($ticket_id) {
        $this->ticket_id = $ticket_id;
    }

    public function setCreated($created) {
        $this->created = $created;
    }

        /* End Setters and Getters */
    /*     * ****************************************************************** */

    /*     * ****************************************************************** */
    /* Static Methods */

    public static function getAll() {
        $items = array();
        $sql = 'SELECT * ' .
                ' FROM ' . EQUIPMENT_TICKET_TABLE;

        $res = db_query($sql);
        if ($res && ($num = db_num_rows($res))) {
            while ($row = db_fetch_array($res)) {
                $item = new Equipment_Ticket($row['ticket_id'], $row['equipment_id']);
                $items[] = $item;
            }
        }

        return $items;
    }
    
     public static function getAllEquipment() {
        $items = array();
        $sql = 'SELECT * ' .
                ' FROM ' . EQUIPMENT_TICKET_TABLE;

        $res = db_query($sql);
        if ($res && ($num = db_num_rows($res))) {
            while ($row = db_fetch_array($res)) {
                $item = new Equipment($row['equipment_id']);
                $items[] = $item;
            }
        }

        return $items;
    }
    
      public static function getAllTickets() {
        $items = array();
        $sql = 'SELECT * ' .
                ' FROM ' . EQUIPMENT_TICKET_TABLE;

        $res = db_query($sql);
        if ($res && ($num = db_num_rows($res))) {
            while ($row = db_fetch_array($res)) {
                $item = Ticket::lookup($row['ticket_id']);
                $items[] = $item;
            }
        }

        return $items;
    }

    public static function findByEquipmentId($id) {
        $items = array();
        $sql = 'SELECT * ' .
                ' FROM ' . EQUIPMENT_TICKET_TABLE .
                ' WHERE equipment_id=' . db_input($id);

        $res = db_query($sql);
        if ($res && ($num = db_num_rows($res))) {
            while ($row = db_fetch_array($res)) {
                $item = new Equipment_Ticket($row['ticket_id'], $row['equipment_id']);
                $items[] = $item;
            }
        }

        return $items;
    }

    public static function findByTicketId($id) {
        $items = array();
        $sql = 'SELECT * ' .
                ' FROM ' . EQUIPMENT_TICKET_TABLE .
                ' WHERE ticket_id=' . db_input($id);

        $res = db_query($sql);
        if ($res && ($num = db_num_rows($res))) {
            while ($row = db_fetch_array($res)) {
                $item = new Equipment_Ticket($row['ticket_id'], $row['equipment_id']);
                $items[] = $item;
            }
        }

        return $items;
    }

    /* End Static Methods */
    /*     * ****************************************************************** */
}
