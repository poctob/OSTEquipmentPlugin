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
namespace model;

class EquipmentDashboard {

    private $equipment;
    private $categories;
    private $status;
    private $tickets;

    public function __construct() {
        $this->equipment = Equipment::getAll();
        $this->categories = array();
        $cats = EquipmentCategory::getAll();

        foreach ($cats as $cat) {
            $cats_data = array();
            $cats_data['name'] = $cat->getName();
            $cats_data['items'] = $cat->countEquipment();
            $cats_data['tickets'] = $cat->countOpenTickets();

            $this->categories[] = $cats_data;
        }

        $this->status = array();
        $stats = EquipmentStatus::getAll();
        foreach ($stats as $stat) {
            $stat_data = array();
            $stat_data['name'] = $stat->getName();
            $stat_data['items'] = $stat->countEquipment();
            
            $this->status[] = $stat_data;
        }

        $this->tickets = 0;

        foreach ($this->categories as $cat) {
            $ticket_count = $cat['tickets'];
            if (isset($ticket_count)) {
                $this->tickets+=$ticket_count;
            }
        }
    }

    /* ------------------> Getter methods <--------------------- */

    function getEquipment() {
        return $this->equipment;
    }

    function getCategories() {
        return $this->categories;
    }

    function getStatus() {
        return $this->status;
    }

    function getTickets() {
        return $this->tickets;
    }

    /* ------------------> Setter methods <--------------------- */

    function setEquipment($data) {
        $this->equipment = $data;
    }

    function setCategories($data) {
        $this->categories = $data;
    }

    function setStatus($data) {
        $this->status = $data;
    }

    function setTickets($data) {
        $this->tickets = $data;
    }

}

?>
