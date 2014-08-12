<?php
/*********************************************************************
    equipment-status.inc.php

    Displays equipment status
 
    Alex P <alexp@xpresstek.net>
    Copyright (c)  2013 XpressTek
    http://www.xpresstek.net

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
**********************************************************************/
if(!defined('OSTCLIENTINC') || !$status ) die('Access Denied');
?>
<h1><strong><?php echo $status->getName() ?></strong></h1>
<p>
<?php echo \Format::safe_html($status->getDescription()); ?>
</p>
<hr>
<?php
$sql='SELECT equipment.equipment_id as equipment_id, equipment.asset_id as Equipment, 
    status.name as Status, status.color as color'
    .' FROM '.EQUIPMENT_TABLE.' equipment '
    .' LEFT JOIN '.EQUIPMENT_STATUS_TABLE.' status ON(status.status_id=equipment.status_id) '
    .' WHERE equipment.ispublished=1 AND equipment.status_id='.db_input($status->getId())
    .' GROUP BY equipment.equipment_id';
if(($res=db_query($sql)) && db_num_rows($res)) {
    echo '
         <h2>Equpment</h2>
         <div id="equipment">
            <ol>';
    while($row=db_fetch_array($res)) {
        echo sprintf('
            <li> <a href="equipment.php?id=%d" %s>%s &nbsp;%s</a></li>',   
             $row['equipment_id'],
             'style="color:'.$row['color'].'"',
             \Format::htmlchars($row['Equipment']), $row['Status']);
    }
    echo '  </ol>
         </div>
         <p><a class="back" href="index.php">&laquo; Go Back</a></p>';
}else {
    echo '<strong>Status does not have any equipment. <a href="index.php">Back To Index</a></strong>';
}
?>
