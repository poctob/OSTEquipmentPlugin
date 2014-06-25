<?php
/*********************************************************************
    equipment_view.inc.php
 
    Alex P <alexp@xpresstek.net>
    Copyright (c)  2013 XpressTek
    http://www.xpresstek.net

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
**********************************************************************/
if(!defined('OSTSTAFFINC') || !$equipment || !$thisstaff) die('Access Denied');

$category=$equipment->getCategory();

?>
<h2>Equipment</h2>
<div id="breadcrumbs">
    <a href="equipment.php">All Categories</a> 
    &raquo; <a href="equipment.php?cid=<?php echo $category->getId(); ?>"><?php echo $category->getName(); ?></a>
    <span class="faded">(<?php echo $category->isPublic()?'Public':'Internal'; ?>)</span>
</div>
<div style="width:700;padding-top:2px; float:left;">
<strong style="font-size:16px;"><?php echo $equipment->getName() ?></strong>&nbsp;&nbsp;

<span class="faded"><?php echo $equipment->isPublished()?'(Published)':'(Internal)'; ?></span>

<span class="faded"><?php echo $equipment->isActive()?'(Active)':'(Retired)'; ?></span>
</div>
<div style="float:right;text-align:right;padding-top:5px;padding-right:5px;">
<?php

    echo sprintf('<a href="equipment_item.php?id=%d&a=edit" class="Icon newHelpTopic">Edit Equipment</a>',
            $equipment->getId());

?>
&nbsp;
</div>
<div class="clear"></div>
<p>
<?php echo Format::safe_html($equipment->getStatus()); ?>
</p>

<div class="faded">&nbsp;Last updated <?php echo Format::db_daydatetime($category->getUpdateDate()); ?></div>
<hr>

   <div>
    <form action="equipment_item.php?id=<?php echo  $equipment->getId(); ?>" method="post">
	 <?php csrf_token(); ?>
        <input type="hidden" name="id" value="<?php echo  $equipment->getId(); ?>">
        <input type="hidden" name="do" value="manage-equipment">
        <div>
            <strong>Options: </strong>
            <select name="a" style="width:200px;">
                <option value="">Select Action</option>
                
                <?php
                if($equipment->isPublished()) { ?>
                <option value="unpublish">Unpublish equipment</option>
                <?php
                }else{ ?>
                <option value="publish">Publish equipment</option>
                <?php
                } ?>
                
                 <?php
                if($equipment->isActive()) { ?>
                <option value="retire">Retire equipment</option>
                <?php
                }else{ ?>
                <option value="activate">Activate equipment</option>
                <?php
                } ?>
                
                <option value="edit">Edit equipment</option>              
            </select>
            &nbsp;&nbsp;<input type="submit" name="submit" value="Go">
        </div>
    </form>
   </div>

<div class="clear"></div>
<div style="width:700;padding-top:2px; float:left;"><p>
    <strong style="font-size:14px;">Open Tickets:</strong>.
    <table>
        <thead>
            <tr>
                <th width="70">Ticket</th>
                <th width="70">Date</th>
                <th width="70">Subject</th>
                <th width="70">From</th>
                <th width="70">Priority</th>
                <th width="100">Assigned To</th>
            </tr>
        </thead>
        <tbody>
      <?php 
        $open_tickets=Equipment::getOpenTickets($equipment->getId());
        foreach($open_tickets as &$ticket_id)
        {
            $ticket=Ticket::lookup($ticket_id);
            if(isset($ticket))
            {?>                
              <tr>
                  <td align="center" >
                 <a class="Icon Ticket ticketPreview" title="Preview Ticket" 
                    href="tickets.php?id=<?php echo $ticket->getId(); ?>"><?php echo $ticket->getNumber(); ?></a>
                  </td>
                   <td align="center" >
                       <?php echo Format::db_date($ticket->getCreateDate()); ?>
                   </td>
                    <td align="center" >
                       <?php echo $ticket->getSubject(); ?>
                   </td>
                    <td align="center" >
                       <?php echo $ticket->getName(); ?>
                   </td>
                     <td align="center" >
                       <?php echo $ticket->getPriority(); ?>
                   </td>
                    <td align="center" >
                       <?php 
                                $staff=$ticket->getStaff();
                                if(isset($staff))
                                    echo $staff->getName();
                       ?>
                   </td>
              </tr>
            <?php }
        }
    ?>
             </tbody> </table>
</div>

<div class="clear"></div>
<div style="width:700;padding-top:2px; float:left;"><p>
    <strong style="font-size:14px;">Ticket History:</strong>.
    <table>
        <thead>
            <tr>
                <th width="70">Ticket</th>
                <th width="70">Date</th>
                <th width="70">Subject</th>
                <th width="70">From</th>
                <th width="70">Priority</th>
                <th width="100">Closed By</th>
            </tr>
        </thead>
        <tbody>
      <?php 
        $closed_tickets=Equipment::getClosedTickets($equipment->getId());
        foreach($closed_tickets as &$ticket_id)
        {
            $ticket=Ticket::lookup($ticket_id);
            if(isset($ticket))
            {?>                
              <tr>
                  <td align="center" >
                 <a class="Icon Ticket ticketPreview" title="Preview Ticket" 
                    href="tickets.php?id=<?php echo $ticket->getId(); ?>"><?php echo $ticket->getNumber(); ?></a>
                  </td>
                   <td align="center" >
                       <?php echo Format::db_date($ticket->getCreateDate()); ?>
                   </td>
                    <td align="center" >
                       <?php echo $ticket->getSubject(); ?>
                   </td>
                    <td align="center" >
                       <?php echo $ticket->getName(); ?>
                   </td>
                     <td align="center" >
                       <?php echo $ticket->getPriority(); ?>
                   </td>
                    <td align="center" >
                       <?php echo $ticket->getStaff()->getName(); ?>
                   </td>
              </tr>
            <?php }
        }
    ?>
             </tbody> </table>
</div>
<div class="clear"></div>

