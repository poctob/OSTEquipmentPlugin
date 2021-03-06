<?php

namespace model;

/* * *******************************************************************
  class.equipment.php

  Backend support for equipment, creates, edits, deletes.

  Copyright (c)  2006-2014 XpressTek
  http://www.xpresstek.net

  Released under the GNU General Public License WITHOUT ANY WARRANTY.
  See LICENSE.TXT for details.

  vim: expandtab sw=4 ts=4 sts=4:
 * ******************************************************************** */
require_once(INCLUDE_DIR . 'class.dynamic_forms.php');

class Equipment extends Entity {

    private $equipment_id;
    private $asset_id;
    private $category_id;
    private $status_id;
    private $ispublished;
    private $created;
    private $updated;
    private $is_active;
    private $user_id;
    private $staff_id;

    public function getJsonProperties() {
        return array(
            'id' => $this->getId(),
            'equipment_id' => $this->getEquipment_id(),
            'asset_id' => $this->getAsset_id(),
            'category' => $this->getCategory()->getName(),
            'category_id' => $this->getCategory_id(),
            'status' => $this->getStatus()->getName(),
            'status_id' => $this->getStatus_id(),
            'ispublished' => $this->getIspublished() ? 'Yes' : 'No',
            'created' => $this->getCreated(),
            'updated' => $this->getUpdated(),
            'is_active' => $this->getIs_active() ? 'Yes' : 'No',
            'staff' => $this->getStaff()->getName()->getFull()
        );
    }

    /* ------------------> Getter methods <--------------------- */

    function getId() {
        return $this->equipment_id;
    }

    public function getEquipment_id() {
        return $this->equipment_id;
    }

    public function getAsset_id() {
        return $this->asset_id;
    }

    public function getCategory_id() {
        return $this->category_id;
    }

    public function getStatus_id() {
        return $this->status_id;
    }

    public function getIspublished() {
        return $this->ispublished;
    }

    public function getCreated() {
        return $this->created;
    }

    public function getUpdated() {
        return $this->updated;
    }

    public function getIs_active() {
        return $this->is_active;
    }

    public function getCategory() {
        if ($this->category_id > 0) {
            return new EquipmentCategory($this->category_id);
        }
        return null;
    }

    public function getStatus() {
        if ($this->status_id > 0) {
            return new EquipmentStatus($this->status_id);
        }
        return null;
    }

    public function getUser_id() {
        return $this->user_id;
    }

    public function getUser() {
        return \User::lookup($this->user_id);
    }

    public function getStaff_id() {
        return $this->staff_id;
    }

    public function getStaff() {
        return new \Staff($this->staff_id);
    }

    /* ------------------> Setter methods <--------------------- */

    public function setId($id) {
        $this->setEquipment_id($id);
    }

    public function setEquipment_id($equipment_id) {
        $this->equipment_id = $equipment_id;
    }

    public function setAsset_id($asset_id) {
        $this->asset_id = $asset_id;
    }

    public function setCategory_id($category_id) {
        $this->category_id = $category_id;
    }

    public function setStatus_id($status_id) {
        $this->status_id = $status_id;
    }

    public function setIspublished($ispublished) {
        $this->ispublished = $ispublished;
    }

    public function setCreated($created) {
        $this->created = $created;
    }

    public function setUpdated($updated) {
        $this->updated = $updated;
    }

    public function setIs_active($is_active) {
        $this->is_active = $is_active;
    }

    public function setUser_id($user_id) {
        $this->user_id = $user_id;
    }

    public function setStaff_id($staff_id) {
        $this->staff_id = $staff_id;
    }

    public static function getOpenTickets($id) {
        $ticket_ids = array();
        $sql = 'SELECT ticket_id, equipment_id FROM ' . EQUIPMENT_TICKET_VIEW . ' '
                . 'WHERE equipment_id=' . db_input($id) . ' '
                . 'AND status="open"';
        $res = db_query($sql);
        if ($res && ($num = db_num_rows($res))) {
            while ($row = db_fetch_array($res)) {
                $ticket_ids[] = $row;
            }
        }
        return $ticket_ids;
    }

    public static function getClosedTickets($id) {
        $ticket_ids = array();
         $sql = 'SELECT ticket_id, equipment_id FROM ' . EQUIPMENT_TICKET_VIEW . ' '
                . 'WHERE equipment_id=' . db_input($id) . ' '
                . 'AND status="closed"';
        $res = db_query($sql);
        if ($res && ($num = db_num_rows($res))) {
            while ($row = db_fetch_array($res)) {
                $ticket_ids[] = $row;
            }
        }

        return $ticket_ids;
    }

    public static function getTicketList($type, $id) {
        return $type == 'open' ? static::getOpenTickets($id) : static::getClosedTickets($id);
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
    public static function assignTicket($ticket_id, $id) {

        self::onCloseTicket($ticket_id, $id, true);
        self::deleteByTicket($ticket_id);

        $sql = 'equipment_id=' . db_input($id)
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
    public static function onCloseTicket($ticket_id, $eq_id = 0, $force = false) {

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
                    $eq->setStatus_Id($b_status->getId());
                    $eq->save();
                }
            }
        }
    }

    function delete() {
        $sql = 'DELETE FROM ' . EQUIPMENT_TICKET_TABLE
                . ' WHERE equipment_id=' . db_input($this->getId());
        $success = db_query($sql);

        if ($success) {
            return parent::delete();
        }
    }

    /* ------------------> Static methods <--------------------- */

    public static function countPublishedEquipment() {
        $sql = 'SELECT count(equipment.equipment_id) '
                . ' FROM ' . EQUIPMENT_TABLE . ' equipment '
                . ' INNER JOIN ' . EQUIPMENT_CATEGORY_TABLE . ' cat ON(cat.category_id=equipment.category_id AND cat.ispublic=1) '
                . ' WHERE equipment.ispublished=1';

        return db_result(db_query($sql));
    }

    public static function findIdByAssetId($asset_id) {
        $sql = 'SELECT equipment_id FROM ' . EQUIPMENT_TABLE
                . ' WHERE asset_id=' . db_input($asset_id);

        list($id) = db_fetch_row(db_query($sql));

        return $id;
    }

    public static function findByStaffId($staff_id) {
        $items = array();
        $sql = 'SELECT equipment_id FROM ' . EQUIPMENT_TABLE
                . ' WHERE staff_id=' . db_input($staff_id);

        $res = db_query($sql);
        if ($res && ($num = db_num_rows($res))) {
            while ($row = db_fetch_array($res)) {
                $items[] = new \model\Equipment($row['equipment_id']);
            }
        }
        return $items;
    }

    public static function findByNotStaffId($staff_id) {
        $items = array();
        $sql = 'SELECT equipment_id FROM ' . EQUIPMENT_TABLE
                . ' WHERE staff_id IS NULL OR staff_id !=' . db_input($staff_id);

        $res = db_query($sql);
        if ($res && ($num = db_num_rows($res))) {
            while ($row = db_fetch_array($res)) {
                $items[] = new \model\Equipment($row['equipment_id']);
            }
        }
        return $items;
    }

    public static function findByStatusAndCategory($status_id, $category_id) {
        $items = array();
        $sql = 'SELECT equipment_id FROM ' . EQUIPMENT_TABLE
                . ' WHERE status_id=' . db_input($status_id)
                . ' AND category_id=' . db_input($category_id);

        $res = db_query($sql);
        if ($res && ($num = db_num_rows($res))) {
            while ($row = db_fetch_array($res)) {
                $items[] = new \model\Equipment($row['equipment_id']);
            }
        }
        return $items;
    }

    public static function search($needle) {
        $key = '%' . $needle . '%';
        $items = array();
        $sql = 'SELECT DISTINCT equipment_id FROM ' . EQUIPMENT_SEARCH_VIEW
                . ' WHERE asset_id LIKE ' . db_input($key)
                . ' OR `value` LIKE ' . db_input($key);

        $res = db_query($sql);
        if ($res && ($num = db_num_rows($res))) {
            while ($row = db_fetch_array($res)) {
                $items[] = new \model\Equipment($row['equipment_id']);
            }
        }
        return $items;
    }

    public static function findByAssetId($asset_id) {

        if (($id = self::findIdByAssetId($asset_id))) {
            return self::lookup($id);
        }

        return false;
    }

    public static function findIdByTicket($ticket) {
        $sql = 'SELECT equipment_id FROM ' . EQUIPMENT_TICKET_TABLE
                . ' WHERE ticket_id=' . db_input($ticket);

        list($id) = db_fetch_row(db_query($sql));

        return $id;
    }

    public static function getTicketFormId() {
        $sql = 'SELECT id FROM ' . FORM_SEC_TABLE
                . ' WHERE title=' . db_input('Equipment');

        list($id) = db_fetch_row(db_query($sql));

        return $id;
    }

    public static function deleteByTicket($ticket) {
        $sql = 'DELETE FROM ' . EQUIPMENT_TICKET_TABLE
                . ' WHERE ticket_id=' . db_input($ticket);
        return db_query($sql);
    }

    public static function findByTicket($ticket) {

        if (($id = self::findIdByTicket($ticket)))
            return self::lookup($id);

        return false;
    }

    public function postSave($data) {
        if (isset($data['form_id'])) {
            static::saveDynamicData($data['form_id'], $this->getId(), $data);
        }
    }

    public static function saveDynamicData($form_id, $id, $data) {
        if (intval($id) > 0) {
            $form = \DynamicForm::lookup($form_id);

            if (isset($form)) {
                $form_entry = static::getDynamicData($id);
                $one = $form_entry->one();
                if (isset($one)) {
                    $one->getSaved();
                } else {
                    $one = $form->instanciate();
                    $one->set('object_type', 'E');
                    $one->setObjectId($id);
                }
                $one->setSource($data);
                $one->save();
            }
        }
    }

    public static function getDynamicData($id) {
        return \DynamicFormEntry::objects()
                        ->filter(array('object_id' => $id,
                            'object_type' => 'E'));
    }

    protected function getSaveSQL() {
        $created = $this->equipment_id > 0 ? $this->created : 'NOW()';
        $sql = 'asset_id=' . db_input($this->asset_id) .
                ',category_id=' . db_input($this->category_id) .
                ',status_id=' . db_input($this->status_id) .
                ',ispublished=' . db_input($this->ispublished) .
                ',updated= NOW()' .
                ',created=' . $created .
                ',is_active=' . db_input($this->is_active) .
                ',user_id=' . db_input($this->user_id) .
                ',staff_id=' . db_input($this->staff_id);
        return $sql;
    }

    protected function init() {
        $this->equipment_id = 0;
        $this->asset_id = 0;
        $this->category_id = 0;
        $this->status_id = 0;
        $this->ispublished = 0;
        $this->created = null;
        $this->updated = null;
        $this->is_active = 0;
        $this->staff_id = null;
        $this->user_id = null;
    }

    protected function validate() {
        $retval = isset($this->asset_id);
        if (!$retval) {
            $this->addError('Invalid Asset ID!');
            return $retval;
        }

        $retval = isset($this->category_id) && $this->category_id > 0;
        if (!$retval) {
            $this->addError('Invalid Category ID!');
            return $retval;
        }

        $retval = isset($this->status_id) && $this->status_id > 0;
        if (!$retval) {
            $this->addError('Invalid Status ID!');
            return $retval;
        }

        return $retval;
    }

    protected static function getIdColumn() {
        return 'equipment_id';
    }

    /**
     * @return string
     */
    protected static function getTableName() {
        return EQUIPMENT_TABLE;
    }

}

?>
