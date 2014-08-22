<?php

namespace model;

/**
 * Entity class for equipment recurring ticket.
 *
 * @author Alex Pavlunenko 
 */
class EquipmentTicket extends Entity {

    private $equipment_id;
    private $ticket_id;
    private $created;

    protected function getId() {
        
    }

    protected function getSaveSQL() {
        
    }

    protected function init() {
        
    }

    protected function setId($id) {
        $this->equipment_id = 0;
        $this->ticket_id = 0;
        $this->created = null;
    }

    protected static function getIdColumn() {
        
    }

    protected static function getTableName() {
        return EQUIPMENT_TICKET_TABLE;
    }

    public function getJsonProperties() {
        
    }

    protected function validate() {
        return false;
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

    public function getTicket() {
        if ($this->ticket_id > 0) {
            return Ticket::lookup($this->ticket_id);
        }
        return null;
    }

    public function getCreated() {
        return $this->created;
    }

    /* End Setters and Getters */
    /*     * ****************************************************************** */

    /*     * ****************************************************************** */
    /* Static Methods */

    public static function getAllEquipment() {
        $items = array();
        $sql = 'SELECT DISTINCT(equipment_id) as equipment_id' .
                ' FROM ' . EQUIPMENT_TICKET_TABLE;

        $res = db_query($sql);
        if ($res && ($num = db_num_rows($res))) {
            while ($row = db_fetch_array($res)) {
                $item = new Equipment($row['equipment_id']);
                if (isset($item)) {
                    $items[] = $item;
                }
            }
        }

        return $items;
    }

    public static function getAllTickets() {
        $items = array();
        $sql = 'SELECT DISTINCT(ticket_id) as ticket_id ' .
                ' FROM ' . EQUIPMENT_TICKET_TABLE;

        $res = db_query($sql);
        if ($res && ($num = db_num_rows($res))) {
            while ($row = db_fetch_array($res)) {
                $item = \Ticket::lookup($row['ticket_id']);
                if (isset($item)) {
                    $items[] = $item;
                }
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
                $item = new EquipmentTicket($row['ticket_id'],
                        $row['equipment_id']);
                if (isset($item)) {
                    $items[] = $item;
                }
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
                $item = new EquipmentTicket($row['ticket_id'],
                        $row['equipment_id']);
                if (isset($item)) {
                    $items[] = $item;
                }
            }
        }

        return $items;
    }

    /* End Static Methods */
    /*     * ****************************************************************** */
}
