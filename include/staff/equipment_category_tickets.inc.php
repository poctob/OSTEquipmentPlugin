<?php
/*********************************************************************
    equipment_category_ticket.inc.php
 
    Alex P <alexp@xpresstek.net>
    Copyright (c)  2014 XpressTek
    http://www.xpresstek.net

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
**********************************************************************/
require_once(EQUIPMENT_INCLUDE_DIR . 'class.equipment_category.php');
?>
<h2>Equipment</h2>
<div id="breadcrumbs">
    <a href="equipment_categories.php">All Categories</a> 
    &raquo; <a href="equipment_categories.php?id=<?php echo $category->getId(); ?>&tickets=<?php echo $tickets_status; ?>"><?php echo $category->getName(); ?></a>
    <span class="faded">(<?php echo $category->isPublic()?'Public':'Internal'; ?>)</span>
</div>
<?php
     $label = 'Unknown';
     $page ='#';
    if(isset($tickets_status) && $tickets_status == 'open')
    {
        $label='Open';
        $page='equipment_view_open_tickets.inc.php';
    }
    else if(isset($tickets_status) && $tickets_status == 'closed')
    {
        $label='Closed';
        $page='equipment_view_closed_tickets.inc.php';
    }
    
    $equipment_tickets = 
            Equipment_Category::getTicketList($tickets_status, $category->getId());
?>
<div style="width:700;padding-top:2px; float:left;">
<strong style="font-size:16px;"><?php echo $label.' tickets in '.$category->getName().' category:' ?></strong>
</div>
<?php
    require(EQUIPMENT_STAFFINC_DIR.$page);
?>

