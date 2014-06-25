<?php
/*********************************************************************
    index.php

    Equipment index
 
    Alex P <alexp@xpresstek.net>
    Copyright (c)  2013 XpressTek
    http://www.xpresstek.net

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
**********************************************************************/
require('equipment.inc.php');
require_once(EQUIPMENT_INCLUDE_DIR.'class.equipment_category.php');
$inc='equipment_list.inc.php';
require(CLIENTINC_DIR.'header.inc.php');
require(EQUIPMENT_CLIENTINC_DIR.$inc);
require(CLIENTINC_DIR.'footer.inc.php');
?>
