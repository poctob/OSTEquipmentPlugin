<?php

/* * *******************************************************************
  class.equipment.php

  Backend support for equipmnet, creates, edits, deletes.

  Copyright (c)  2006-2013 XpressTek
  http://www.xpresstek.net

  Released under the GNU General Public License WITHOUT ANY WARRANTY.
  See LICENSE.TXT for details.

  vim: expandtab sw=4 ts=4 sts=4:
 * ******************************************************************** */
require_once('class.file.php');
require_once('class.equipment_category.php');
require_once('class.equipment_status.php');
require_once(INCLUDE_DIR . 'class.dynamic_forms.php');

class Equipment {

    var $id;
    var $asset_id;
    var $ht;
    var $category;
    var $status;

    function Equipment($id = 0) {
        $this->id = 0;
        $this->ht = array();
        $this->load($id);
    }

    function load($id) {

        $sql = 'SELECT equipment.*,cat.ispublic, status.name as Status, 
            status.image as Image, status.color as Color '
                . ' FROM ' . EQUIPMENT_TABLE . ' equipment '
                . ' LEFT JOIN ' . EQUIPMENT_CATEGORY_TABLE . ' cat ON(cat.category_id=equipment.category_id) '
                . ' LEFT JOIN ' . EQUIPMENT_STATUS_TABLE . ' status ON(equipment.status_id=status.status_id) '
                . ' WHERE equipment.equipment_id=' . db_input($id)
                . ' GROUP BY equipment.equipment_id';

        if (!($res = db_query($sql)) || !db_num_rows($res))
            return false;

        $this->ht = db_fetch_array($res);
        $this->ht['id'] = $this->id = $this->ht['equipment_id'];
        $this->asset_id = $this->ht['asset_id'];

        if ($this->getCategoryId())
            $this->category = Equipment_Category::lookup($this->getCategoryId());

        if ($this->getStatusId())
            $this->status = Equipment_Status::lookup($this->getStatusId());


        return true;
    }

    function reload() {
        return $this->load($this->getId());
    }

    /* ------------------> Getter methods <--------------------- */

    function getId() {
        return $this->id;
    }

    function getAssetId() {
        return $this->asset_id;
    }

    function getHashtable() {
        return $this->ht;
    }

    function getName() {
        return $this->ht['name'];
    }

    function getDescription() {
        return $this->ht['description'];
    }

    function getNotes() {
        return $this->ht['notes'];
    }

    function getStatus() {
        return $this->status;
    }

    function getStatusName() {
        return $this->status->name;
    }

    function getStatusId() {
        return $this->ht['status_id'];
    }

    function getColor() {
        return $this->ht['Color'];
    }

    function getImage() {
        return $this->ht['Image'];
    }

    function isPublished() {
        return ($this->ht['ispublished']);
    }

    function isActive() {
        return $this->ht['is_active'];
    }

    function getCreateDate() {
        return $this->ht['created'];
    }

    function getUpdateDate() {
        return $this->ht['updated'];
    }

    function getCategoryId() {
        return $this->ht['category_id'];
    }

    function getCategory() {


        return $this->category;
    }

    /* ------------------> Setter methods <--------------------- */

    function setPublished($val) {
        $this->ht['ispublished'] = $val;
    }

    function setName($name) {
        $this->ht['name'] = Format::striptags(trim($name));
    }

    function setDescription($text) {
        $this->ht['description'] = $text;
    }

    function setAssetId($val) {
        $this->ht['asset_id'] = $val;
        $this->asset_id = $val;
    }

    function setNotes($text) {
        $this->ht['notes'] = $text;
    }

    function setStatus($status) {
        $this->ht['Status'] = $status;
    }

    function setStatusID($status_id) {
        $this->ht['status_id'] = $status_id;
    }

    /* For ->attach() and ->detach(), use $this->attachments() */

    function attach($file) {
        return $this->_attachments->add($file);
    }

    function detach($file) {
        return $this->_attachments->remove($file);
    }

    function setActive($val) {
        $this->ht['is_active'] = $val;
    }

    function activate() {
        $this->setActive(1);
        return $this->apply();
    }

    function retire() {
        $this->setActive(0);
        return $this->apply();
    }

    function publish() {
        $this->setPublished(1);

        return $this->apply();
    }

    function unpublish() {
        $this->setPublished(0);

        return $this->apply();
    }

    /* Same as update - but mainly called after one or more setters are changed. */

    function apply() {
        //XXX: set errors and add ->getErrors() & ->getError()
        return $this->update($this->ht, $errors);               # nolint
    }

    function getEquipment($publishedOnly = false) {

        $equipment = array();
        $sql = 'SELECT equipment_id, name, asset_id '
                . ' FROM ' . EQUIPMENT_TABLE
                . ' WHERE is_active=1';

        if ($publishedOnly)
            $sql.=' AND ispublished=1';

        $sql.=' ORDER BY name';
        if (($res = db_query($sql)) && db_num_rows($res))
            while (list($id, $name) = db_fetch_row($res))
                $equipment[$id] = $name;

        return $equipment;
    }

    function getPublishedEquipment() {
        return self::getEquipment(true);
    }

    public static function getOpenTickets($id) {
        $ticket_ids = array();
        $sql = 'SELECT et.ticket_id'
                . ' FROM ' . EQUIPMENT_TICKET_TABLE . ' et '
                . ' LEFT JOIN ' . TICKET_TABLE . ' ticket ON(et.ticket_id=ticket.ticket_id) '
                . ' WHERE et.equipment_id=' . db_input($id)
                . ' AND ticket.status=\'open\'';
        if (($res = db_query($sql)) && db_num_rows($res))
            while (list($id) = db_fetch_row($res))
                $ticket_ids[] = $id;

        return $ticket_ids;
    }

    public static function getClosedTickets($id) {
        $ticket_ids = array();
        $sql = 'SELECT et.ticket_id'
                . ' FROM ' . EQUIPMENT_TICKET_TABLE . ' et '
                . ' LEFT JOIN ' . TICKET_TABLE . ' ticket ON(et.ticket_id=ticket.ticket_id) '
                . ' WHERE et.equipment_id=' . db_input($id)
                . ' AND ticket.status=\'closed\'';
        if (($res = db_query($sql)) && db_num_rows($res))
            while (list($id) = db_fetch_row($res))
                $ticket_ids[] = $id;

        return $ticket_ids;
    }

    public static function getTicketList($type, $id) {
        return $type == 'open' ? self::getOpenTickets($id) : self::getClosedTickets($id);
    }

    /**
     * Assigns a ticket to the equipment.
     * First we check if this ticket has any equipment already assigned to it,
     * this is done in case of the update, if there is any equipment assigned,
     * it is deleted from the equipment_ticket table.  Regular on_close checks
     * are ran as well.
     * @param type $ticket_id  Id of the ticket to assign equipment to.
     * @return boolean if operation is succes, True.  False if somethign goes
     * wrong
     */
    function assignTicket($ticket_id) {

        self::onCloseTicket($ticket_id, $this->getId(), true);
        self::deleteByTicket($ticket_id);

        $sql = 'equipment_id=' . db_input($this->getId())
                . ', ticket_id=' . db_input($ticket_id);

        $sql = 'INSERT INTO ' . EQUIPMENT_TICKET_TABLE . ' SET ' . $sql . ',created=NOW()';

        if (!db_query($sql) || !db_affected_rows()) {
            return false;
        }

        return true;
    }

    /**
     * This function is overruning its original scope, but it seems logical
     * to put that functionality there.  
     * It finds equipment associated with the supplied ticket id, checks if this
     * ticket is the only one associated with the equipment and will reset
     * equipment status to its baseline.
     * 
     * @param int $ticket_id Ticket id to lookup
     * @param int $eq_id Optional, equipment id to ignore, if this id is 
     * associated with the ticket, it will not be reset.  This is used when
     * a ticket is updated and new status is selected for existing equipment. 
     * @param bool $force Whether to force status reset.  This is used when new
     * equipment is selected during ticket update.  If there is only one ticket
     * associated with this equipment, the equipment status will be reset.
     * @return void
     */
    function onCloseTicket($ticket_id, $eq_id = 0, $force = false) {

        $eq = self::findByTicket($ticket_id);
        if ($eq) {
            if ($eq->getId() == $eq_id) {
                return;
            }

            $open_tickets = self::getOpenTickets($eq->getId());

            $do_close = (count($open_tickets) == 0);
            if (!$do_close) {
                $do_close = $force &&
                        count($open_tickets) == 1 &&
                        $open_tickets[0] == $ticket_id;
            }
            if ($do_close) {
                $b_status = Equipment_Status::getBaselineStatus();

                if ($b_status) {
                    $eq->setStatusID($b_status->getId());
                    $eq->apply();
                }
            }
        }
    }

    function update($vars, &$errors) {
        if (!$this->save($this->getId(), $vars, $errors))
            return false;

        $this->reload();

        return true;
    }

    function delete() {

        $sql = 'UPDATE ' . EQUIPMENT_TABLE
                . ' SET is_active=0'
                . ' WHERE equipment_id=' . db_input($this->getId())
                . ' LIMIT 1';
        if (!db_query($sql) || !db_affected_rows())
            return false;

        return true;
    }

    /* ------------------> Static methods <--------------------- */

    function add($vars, &$errors) {
        if (!($id = self::create($vars, $errors)))
            return false;

        if (($equipment = self::lookup($id))) {
            $equipment->reload();
        }

        return $equipment;
    }

    function create($vars, &$errors) {
        return self::save(0, $vars, $errors);
    }

    public static function lookup($id) {
        return ($id && is_numeric($id) &&
                ($obj = new Equipment($id)) && $obj->getId() == $id) ? $obj : null;
    }

    function countPublishedEquipment() {
        $sql = 'SELECT count(equipment.equipment_id) '
                . ' FROM ' . EQUIPMENT_TABLE . ' equipment '
                . ' INNER JOIN ' . EQUIPMENT_CATEGORY_TABLE . ' cat ON(cat.category_id=equipment.category_id AND cat.ispublic=1) '
                . ' WHERE equipment.ispublished=1';

        return db_result(db_query($sql));
    }

    public static function findIdByName($name) {
        $sql = 'SELECT equipment_id FROM ' . EQUIPMENT_TABLE
                . ' WHERE name=' . db_input($name);

        list($id) = db_fetch_row(db_query($sql));

        return $id;
    }

    public static function findIdByAssetId($asset_id) {
        $sql = 'SELECT equipment_id FROM ' . EQUIPMENT_TABLE
                . ' WHERE asset_id=' . db_input($asset_id);

        list($id) = db_fetch_row(db_query($sql));

        return $id;
    }

    public static function findByName($name) {

        if (($id = self::findIdByName($name)))
            return self::lookup($id);

        return false;
    }

    public static function findByAssetId($asset_id) {

        if (($id = self::findIdByAssetId($asset_id)))
            return self::lookup($id);

        return false;
    }

    function findIdByTicket($ticket) {
        $sql = 'SELECT equipment_id FROM ' . EQUIPMENT_TICKET_TABLE
                . ' WHERE ticket_id=' . db_input($ticket);

        list($id) = db_fetch_row(db_query($sql));

        return $id;
    }

    function deleteByTicket($ticket) {
        $sql = 'DELETE FROM ' . EQUIPMENT_TICKET_TABLE
                . ' WHERE ticket_id=' . db_input($ticket);
        return db_query($sql);
    }

    function findByTicket($ticket) {

        if (($id = self::findIdByTicket($ticket)))
            return self::lookup($id);

        return false;
    }

    public static function save($id, $vars, &$errors, $validation = false) {

        //Cleanup.
        $vars['name'] = Format::striptags(trim($vars['name']));

        //validate
        if ($id && $id != $vars['id'])
            $errors['err'] = 'Internal error. Try again';

        if (!$vars['asset_id'])
            $errors['asset_id'] = 'Asset ID is required';
        elseif (($qid = self::findIdByAssetId($vars['asset_id'])) && $qid != $id) {
            $errors['asset_id'] = 'Asset ID already exists';
        }

        if (!$vars['category_id'] || !($category = Equipment_Category::lookup($vars['category_id'])))
            $errors['category_id'] = 'Category is required';

        if (!$vars['status_id'] || !($status = Equipment_Status::lookup($vars['status_id'])))
            $errors['status_id'] = 'Status is required';

        if ($errors || $validation)
            return (!$errors);

        //save
        $sql = ' updated=NOW() '
                . ', name=' . db_input($vars['name'])
                . ', description=' . db_input(Format::safe_html($vars['description']))
                . ', status_id=' . db_input(Format::safe_html($vars['status_id']))
                . ', category_id=' . db_input($vars['category_id'])
                . ', ispublished=' . db_input(isset($vars['ispublished']) ? $vars['ispublished'] : 0)
                . ', is_active=' . db_input(isset($vars['is_active']) ? $vars['is_active'] : 1)
                . ', notes=' . db_input($vars['notes'])
                . ', asset_id=' . db_input($vars['asset_id']);

        if ($id) {
            $sql = 'UPDATE ' . EQUIPMENT_TABLE . ' SET ' . $sql . ' WHERE equipment_id=' . db_input($id);
            if (!db_query($sql)) {
                $errors['err'] = 'Unable to update Equipment.';
            }
        } else {
            $sql = 'INSERT INTO ' . EQUIPMENT_TABLE . ' SET ' . $sql . ',created=NOW()';
            if (!db_query($sql) || !($id = db_insert_id())) {
                $errors['err'] = 'Unable to create Equipmnet. Internal error';
            }
        }

        if (isset($errors) && count($errors) > 0) {
            return false;
        }

        if (isset($vars['form_id'])) {
            self::saveDynamicData($vars['form_id'], $id, $vars);
        }

        return true;
    }

    public static function getAll() {
        $items = array();
        $sql = 'SELECT equipment_id ' .
                ' FROM ' . EQUIPMENT_TABLE;

        $res = db_query($sql);
        if ($res && ($num = db_num_rows($res))) {
            while ($row = db_fetch_array($res)) {
                $item = new Equipment($row['equipment_id']);
                $items[] = $item->getHashtable();
            }
        }

        return $items;
    }

    public static function saveDynamicData($form_id, $id, $data) {
        if ($id > 0) {
            $form = DynamicForm::lookup($form_id);

            $form_entry = $form->instanciate();
            $form_entry->set('object_type', 'G');
            $form_entry->setObjectId($id);
            foreach ($form_entry->getFields() as $f) {
                if (isset($data[$f->get('name')])) {
                    $form_entry->setAnswer($f->get('name'), $data[$f->get('name')]);
                }
            }
            $form_entry->save();
        }
    }

    public static function getDynamicData($id) {
        return DynamicFormEntry::objects()
                        ->filter(array('object_id' => $id, 
                            'object_type' => 'G'));
    }

}

?>
