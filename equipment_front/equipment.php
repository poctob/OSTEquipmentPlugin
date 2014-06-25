<?php
/*********************************************************************
    equipment.php

    Alex P <alexp@xpresstek.net>
    Copyright (c)  2013 XpressTek
    http://www.xpresstek.net

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
**********************************************************************/
require('equipment.inc.php');
require_once(EQUIPMENT_INCLUDE_DIR.'class.equipment.php');

$equipment=$category=$status=null;
if($_REQUEST['id'] && !($equipment=Equipment::lookup($_REQUEST['id'])))
   $errors['err']='Unknown or invalid equpment';

if(!$equipment && $_REQUEST['cid'] && !($category=Equipment_Category::lookup($_REQUEST['cid'])))
    $errors['err']='Unknown or invalid equipment category';

if(!$equipment && $_REQUEST['status'] && !($status=Equipment_Status::lookup($_REQUEST['status'])))
    $errors['err']='Unknown or invalid equipment status';

$inc='equipment_list.inc.php'; //FAQs landing page.
if($equipment && $equipment->isPublished()) {
    $inc='equipment.inc.php';
} elseif($category && $category->isPublic() && $_REQUEST['a']!='search') {
    $inc='equipment-category.inc.php';
} elseif ($status && $_REQUEST['a']!='search') {
    $inc='equipment-status.inc.php';
}
require_once(CLIENTINC_DIR.'header.inc.php');
require_once(EQUIPMENT_CLIENTINC_DIR.$inc);
require_once(CLIENTINC_DIR.'footer.inc.php');
?>
