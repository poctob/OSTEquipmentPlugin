<?php
/*********************************************************************
    equipment.inc.php

    Alex P <alexp@xpresstek.net>
    Copyright (c)  2013 XpressTek
    http://www.xpresstek.net

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
**********************************************************************/
define('ROOT_PATH','../');
require_once('../client.inc.php');
require_once(EQUIPMENT_INCLUDE_DIR.'class.equipment.php');
/* Bail out if knowledgebase is disabled or if we have no public-published Equipment. */
if(!Equipment::countPublishedEquipment()) {
    header('Location: ../');
    exit;
}

$nav = new UserNav($thisclient, 'equipment');
?>
