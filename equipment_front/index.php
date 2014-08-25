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
define('ROOT_PATH','../');
require_once('../client.inc.php');
if(!\model\Equipment::countPublishedEquipment()) {
    header('Location: ../');
    exit;
}
require_once(CLIENTINC_DIR.'header.inc.php');
$dashboard = new \controller\Dashboard();
if(isset($dashboard))
{
    $dashboard->viewClientPage();
}
require_once(CLIENTINC_DIR.'footer.inc.php');
?>
