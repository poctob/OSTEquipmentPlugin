<?php
/*********************************************************************
    equipment_list.inc.php
 
    Alex P <alexp@xpresstek.net>
    Copyright (c)  2013 XpressTek
    http://www.xpresstek.net

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
**********************************************************************/
if(!defined('OSTSTAFFINC') || !$category || !$thisstaff) die('Access Denied');

?>
<div style="width:700;padding-top:10px; float:left;">
  <h2>Equipment</h2>
</div>
<div style="float:right;text-align:right;padding-top:5px;padding-right:5px;">&nbsp;</div>
<div class="clear"></div>
<br>
<div>
    <strong><?php echo $category->getName() ?></strong>
    <span>(<?php echo $category->isPublic()?'Public':'Internal'; ?>)</span>
    <time>Last updated <?php echo Format::db_daydatetime($category->getUpdateDate()); ?></time>
</div>
<div class="cat-desc">
<?php echo Format::safe_html($category->getDescription()); ?>
</div>
<?php

    echo sprintf('<div class="cat-manage-bar"><a href="equipment_categories.php?id=%d" class="Icon editCategory">Edit Category</a>
             <a href="equipment_categories.php" class="Icon deleteCategory">Delete Category</a>
             <a href="equipment_item.php?cid=%d&a=add" class="Icon newEquipment">Add New Equipment</a></div>',
            $category->getId(),
            $category->getId());

?>
<hr>
<?php


$sql='SELECT equipment_id, equipment.name, ispublished, is_active, status.color as color'
    .' FROM '.EQUIPMENT_STATUS_TABLE.' status '
    .' LEFT JOIN '.EQUIPMENT_TABLE.' equipment ON(equipment.status_id=status.status_id) '
    .' WHERE category_id='.db_input($category->getId())
    .' GROUP BY equipment_id';
if(($res=db_query($sql)) && db_num_rows($res)) {
    echo '<div id="equipment">
            <ol>';
    while($row=db_fetch_array($res)) {
        echo sprintf('
            <li><a href="equipment_item.php?id=%d" class="previewequipment" %s>%s <span>- %s</span> <span>- %s</span></a></li>',
            $row['equipment_id'],
            'style="color:'.$row['color'].'!important;"',
            $row['name'],$row['ispublished']?'Published':'Internal',
            $row['is_active']?'Active':'Retired');
    }
    echo '  </ol>
         </div>';
}else {
    echo '<strong>Category does not have equipment</strong>';
}
?>
