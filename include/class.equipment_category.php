<?php

/* * *******************************************************************
  class.equipment_category.php

  Backend support for equipment categories.

  Copyright (c)  2013 XpressTek
  http://www.xpresstek.net

  Released under the GNU General Public License WITHOUT ANY WARRANTY.
  See LICENSE.TXT for details.

  vim: expandtab sw=4 ts=4 sts=4:
 * ******************************************************************** */

require_once('class.equipment.php');

class Equipment_Category {

    var $id;
    var $ht;
    var $equipment_count;
    var $open_ticket_count;
    var $closed_ticket_count;
    var $name;
    var $ispublic;
    var $updated;

    function Equipment_Category($id = 0) {
        $this->id = 0;
        $this->equipment_count = 0;
        $this->open_ticket_count = 0;
        $this->closed_ticket_count = 0;
        $this->name = '';
        $this->ispublic = false;
        $this->updated = null;
        if ($id > 0) {
            $this->load($id);
        }
    }

    function load($id) {

        $sql = ' SELECT cat.*,count(equipment.equipment_id) as equipments '
                . ' FROM ' . EQUIPMENT_CATEGORY_TABLE . ' cat '
                . ' LEFT JOIN ' . EQUIPMENT_TABLE . ' equipment ON(equipment.category_id=cat.category_id) '
                . ' WHERE cat.category_id=' . db_input($id)
                . ' GROUP BY cat.category_id';

        if (!($res = db_query($sql)) || !db_num_rows($res)) {
            return false;
        }

        $this->ht = db_fetch_array($res);
        $this->id = $this->ht['category_id'];
        $this->equipment_count = $this->ht['equipments'];
        $this->open_ticket_count = $this->countOpenTickets($this->id);
        $this->closed_ticket_count = $this->countClosedTickets($this->id);
        $this->name = $this->ht['name'];
        $this->ispublic = $this->ht['ispublic'] ? 'Public' : 'Private';
        $this->updated = $this->ht['updated'];

        return true;
    }

    function reload() {
        return $this->load($this->getId());
    }

    /* ------------------> Getter methods <--------------------- */

    function getId() {
        return $this->id;
    }

    function getName() {
        return $this->name;
    }

    function getNumEquipment() {
        return $this->equipment_count;
    }

    function getDescription() {
        return $this->ht['description'];
    }

    function getNotes() {
        return $this->ht['notes'];
    }

    function getCreateDate() {
        return $this->ht['created'];
    }

    function getUpdateDate() {
        return $this->updated;
    }

    function isPublic() {
        return ($this->ispublic);
    }

    function getHashtable() {
        return $this->ht;
    }

    public function getOpenTicketCount() {
        return $this->open_ticket_count;
    }

    public function getClosedTicketCount() {
        return $this->closed_ticket_count;
    }

    /* ------------------> Setter methods <--------------------- */

    function setName($name) {
        $this->ht['name'] = $name;
        $this->name = $name;
    }

    function setNotes($notes) {
        $this->ht['notes'] = $notes;
    }

    function setDescription($desc) {
        $this->ht['description'] = $desc;
    }

    /* --------------> Database access methods <---------------- */

    function update($vars, &$errors) {

        if (!$this->save($this->getId(), $vars, $errors))
            return false;

        //TODO: move FAQs if requested.

        $this->reload();

        return true;
    }

    function delete() {

        $sql = 'DELETE FROM ' . EQUIPMENT_CATEGORY_TABLE
                . ' WHERE category_id=' . db_input($this->getId())
                . ' LIMIT 1';
        if (db_query($sql) && ($num = db_affected_rows())) {
            db_query('DELETE FROM ' . EQUIPMENT_TABLE
                    . ' WHERE category_id=' . db_input($this->getId()));
        }

        return $num;
    }

    /* ------------------> Static methods <--------------------- */

    static public function lookup($id) {
        return ($id && is_numeric($id) && ($c = new Equipment_Category($id))) ? $c : null;
    }

    function findIdByName($name) {
        $sql = 'SELECT category_id FROM ' . EQUIPMENT_CATEGORY_TABLE . ' WHERE name=' . db_input($name);
        list($id) = db_fetch_row(db_query($sql));

        return $id;
    }

    /**
     * Counts a number of open tickets in this category.
     * @param type Category Id
     * @return int
     */
    function countOpenTickets($id) {
        $sql = 'SELECT COUNT(ticket_id) FROM ' . EQUIPMENT_TICKET_VIEW . ' '
                . 'WHERE category_id=' . db_input($id) . ' '
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
    function countClosedTickets($id) {
        $sql = 'SELECT COUNT(ticket_id) FROM ' . EQUIPMENT_TICKET_VIEW . ' '
                . 'WHERE category_id=' . db_input($id) . ' '
                . 'AND status="closed"';
        list($count) = db_fetch_row(db_query($sql));
        return $count;
    }

    function findByName($name) {
        if (($id = self::findIdByName($name)))
            return new Equipment_Category($id);

        return false;
    }

    public static function validate($vars, &$errors) {
        return self::save(0, $vars, $errors, true);
    }

    public static function create($vars, &$errors) {
        return self::save(0, $vars, $errors);
    }

    public static function save($id, $vars, &$errors, $validation = false) {

        //Cleanup.
        $vars['name'] = Format::striptags(trim($vars['name']));

        //validate
        if ($id && $id != $vars['id'])
            $errors['err'] = 'Internal error. Try again';

        if (!$vars['name'])
            $errors['name'] = 'Category name is required';
        elseif (strlen($vars['name']) < 3)
            $errors['name'] = 'Name is too short. 3 chars minimum';
        elseif (($cid = self::findIdByName($vars['name'])) && $cid != $id)
            $errors['name'] = 'Category already exists';

        if (!$vars['description'])
            $errors['description'] = 'Category description is required';

        if ($errors)
            return false;

        /* validation only */
        if ($validation)
            return true;

        //save
        $sql = ' updated=NOW() ' .
                ',ispublic=' . db_input(isset($vars['ispublic']) ? $vars['ispublic'] : 0) .
                ',name=' . db_input($vars['name']) .
                ',description=' . db_input(Format::safe_html($vars['description'])) .
                ',notes=' . db_input($vars['notes']);

        if ($id) {
            $sql = 'UPDATE ' . EQUIPMENT_CATEGORY_TABLE . ' SET ' . $sql . ' WHERE category_id=' . db_input($id);
            if (db_query($sql))
                return true;

            $errors['err'] = 'Unable to update Equipment category.';
        } else {
            $sql = 'INSERT INTO ' . EQUIPMENT_CATEGORY_TABLE . ' SET ' . $sql . ',created=NOW()';
            if (db_query($sql) && ($id = db_insert_id()))
                return $id;

            $errors['err'] = 'Unable to create Equipment category. Internal error';
        }

        return false;
    }

    public static function countAll() {
        return db_count('SELECT count(*) FROM ' . EQUIPMENT_CATEGORY_TABLE . ' cat ');
    }

    public static function getAll() {
        $categories = array();
        $sql = 'SELECT category_id ' .
                ' FROM ' . EQUIPMENT_CATEGORY_TABLE;

        $res = db_query($sql);
        if ($res && ($num = db_num_rows($res))) {
            while ($row = db_fetch_array($res)) {
                $category = new Equipment_Category($row['category_id']);
                $categories[] = $category;
            }
        }

        return $categories;
    }

    public static function getEquipment($category_id) {
        $equipment = array();
        $sql = ' SELECT equipment.equipment_id '
                . ' FROM ' . EQUIPMENT_CATEGORY_TABLE . ' cat '
                . ' LEFT JOIN ' . EQUIPMENT_TABLE . ' equipment ON(equipment.category_id=cat.category_id) '
                . ' WHERE cat.category_id=' . db_input($category_id)
        ;
        $res = db_query($sql);

        if ($res && ($num = db_num_rows($res))) {
            while ($row = db_fetch_array($res)) {
                $item = new Equipment($row['equipment_id']);
                $equipment[] = $item;
            }
        }

        return $equipment;
    }

}

?>
