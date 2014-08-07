<?php

/* * *******************************************************************
  equipment_category.php

  Backend support for equipment categories.

  Copyright (c)  2013 XpressTek
  http://www.xpresstek.net

  Released under the GNU General Public License WITHOUT ANY WARRANTY.
  See LICENSE.TXT for details.

  vim: expandtab sw=4 ts=4 sts=4:
 * ******************************************************************** */

namespace model;

class EquipmentCategory extends Entity {

    private $category_id;
    private $description;
    private $notes;
    private $created;
    private $name;
    private $ispublic;
    private $updated;

    public function getJsonProperties() {
        return array(
            'id' => $this->getId(),
            'category_id' => $this->getCategory_id(),
            'description' => $this->getDescription(),
            'notes' => $this->getNotes(),
            'created' => $this->getCreated(),
            'name' => $this->getName(),
            'ispublic' => $this->getIspublic() ? 'Public' : 'Private',
            'updated' => $this->getUpdated(),
            'equipment_count' => $this->countEquipment(),
            'open_ticket_count' => $this->countOpenTickets(),
            'closed_ticket_count' => $this->countClosedTickets()
        );
    }

    /* ------------------> Getters and Setters methods <--------------------- */

    function getId() {
        return $this->getCategory_id();
    }

    public function getCategory_id() {
        return $this->category_id;
    }

    public function getDescription() {
        return $this->description;
    }

    public function getNotes() {
        return $this->notes;
    }

    public function getCreated() {
        return $this->created;
    }

    public function getName() {
        return $this->name;
    }

    public function getIspublic() {
        return $this->ispublic;
    }

    public function getUpdated() {
        return $this->updated;
    }

    public function setCategory_id($category_id) {
        $this->category_id = $category_id;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    public function setNotes($notes) {
        $this->notes = $notes;
    }

    public function setCreated($created) {
        $this->created = $created;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function setIspublic($ispublic) {
        $this->ispublic = $ispublic;
    }

    public function setUpdated($updated) {
        $this->updated = $updated;
    }

    public function getOpenTicketCount() {
        return $this->open_ticket_count;
    }

    public function getClosedTicketCount() {
        return $this->closed_ticket_count;
    }

    public function delete() {

        if (parent::delete()) {
            if (db_query($sql) && ($num = db_affected_rows())) {
                db_query('DELETE FROM ' . EQUIPMENT_TABLE
                        . ' WHERE category_id=' . db_input($this->getId()));
            }
        }
        return $num;
    }

    /**
     * Counts a number of open tickets in this category.
     * @param type Category Id
     * @return int
     */
    public function countOpenTickets() {
        $sql = 'SELECT COUNT(ticket_id) FROM ' . EQUIPMENT_TICKET_VIEW . ' '
                . 'WHERE category_id=' . db_input($this->getId()) . ' '
                . 'AND status="open"';
        list($count) = db_fetch_row(db_query($sql));
        return $count;
    }

    public static function getTicketList($tickets_status, $category_id) {
        $ticket_ids = array();
        $sql = 'SELECT ticket_id, equipment_id from ' . EQUIPMENT_TICKET_VIEW . ' '
                . 'where `status`="' . $tickets_status . '"'
                . ' AND category_id=' . $category_id;

        $res = db_query($sql);
        if ($res && ($num = db_num_rows($res))) {
            while ($row = db_fetch_array($res)) {
                $ticket_ids[] = $row;
            }
        }

        return $ticket_ids;
    }

    /**
     * Counts a number of closed tickets in this category.
     * @param type Category Id
     * @return int
     */
    public function countClosedTickets() {
        $sql = 'SELECT COUNT(ticket_id) FROM ' . EQUIPMENT_TICKET_VIEW . ' '
                . 'WHERE category_id=' . db_input($this->getId()) . ' '
                . 'AND status="closed"';
        list($count) = db_fetch_row(db_query($sql));
        return $count;
    }

    public function validate() {
        return false;
    }

    public static function countAll() {
        return db_count('SELECT count(*) FROM ' . EQUIPMENT_CATEGORY_TABLE . ' cat ');
    }

    public function countEquipment() {
        return count($this->getEquipmentIds());
    }

    private function getEquipmentIds() {
        $ids = array();
        $sql = ' SELECT equipment.equipment_id as equipment_id'
                . ' FROM ' . EQUIPMENT_CATEGORY_TABLE . ' cat '
                . ' LEFT JOIN ' . EQUIPMENT_TABLE . ' equipment ON(equipment.category_id=cat.category_id) '
                . ' WHERE cat.category_id=' . db_input($this->getId())
        ;
        $res = db_query($sql);
        if ($res && ($num = db_num_rows($res))) {
            while ($row = db_fetch_array($res)) {
                $ids[] = $row['equipment_id'];
            }
        }

        return $ids;
    }

    public function getEquipment() {

        $ids = $this->getEquipmentIds();
        $equipment = array();
        foreach ($ids as $id) {
            $item = new Equipment($id['equipment_id']);
            $equipment[] = $item;
        }

        return $equipment;
    }

    protected function getSaveSQL() {
        
    }

    protected function init() {
        $this->category_id = 0;
        $this->description = '';
        $this->notes = '';
        $this->created = null;
        $this->name = '';
        $this->ispublic = 0;
        $this->updated = null;
    }

    protected function setId($id) {
        $this->setCategory_id($id);
    }

    protected static function getIdColumn() {
        return 'category_id';
    }

    protected static function getTableName() {
        return EQUIPMENT_CATEGORY_TABLE;
    }

}

?>
