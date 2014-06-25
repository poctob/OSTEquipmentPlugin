<?php

/*********************************************************************
    equipment.inc.php

    Displays a single equipment item
 
    Alex P <alexp@xpresstek.net>
    Copyright (c)  2013 XpressTek
    http://www.xpresstek.net

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
**********************************************************************/
if(!defined('OSTCLIENTINC') || !$equipment  || !$equipment->isPublished()) die('Access Denied');

$category=$equipment->getCategory();

?>
<h1>Equipment</h1>
<div id="breadcrumbs">
    <a href="index.php">All Categories</a>
    &raquo; <a href="equipment.php?cid=<?php echo $category->getId(); ?>"><?php echo $category->getName(); ?></a>
</div>
<div style="width:700;padding-top:2px; float:left;">
<strong style="font-size:16px;"><?php echo $equipment->getName() ?></strong>
</div>
<div style="float:right;text-align:right;padding-top:5px;padding-right:5px;"></div>
<div class="clear"></div>
<p>
<img src="<?php echo "../images/".$equipment->getImage();?>" width="20" height="20"/>
<?php echo Format::safe_html($equipment->getStatus()); ?>

</p>
<hr>
<div class="faded">&nbsp;Last updated <?php echo Format::db_daydatetime($category->getUpdateDate()); ?></div>
