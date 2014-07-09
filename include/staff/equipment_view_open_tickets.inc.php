<div class="clear"></div>
<div style="width:700;padding-top:2px; float:left;"><p>
        <strong style="font-size:14px;">Open Tickets:</strong>.
    <table class="list" border="0" cellspacing="1" cellpadding="2" width="940">
        <thead>
            <tr>
                <th width="70">Ticket</th>
                <th width="150">Opened On</th>
                <th width="70">Subject</th>
                <th width="70">From</th>
                <th width="70">Priority</th>
                <th width="100">Assigned To</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (isset($equipment_tickets)) {
                foreach ($equipment_tickets as &$ticket_id) {
                    $ticket = Ticket::lookup($ticket_id);
                    if (isset($ticket)) {
                        ?>                
                        <tr>
                            <td align="center" >
                                <a class="Icon Ticket ticketPreview" title="Preview Ticket" 
                                   href="tickets.php?id=<?php echo $ticket->getId(); ?>"><?php echo $ticket->getNumber(); ?></a>
                            </td>
                            <td align="center" >
            <?php echo Format::db_datetime($ticket->getCreateDate()); ?>
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
            $staff = $ticket->getStaff();
            if (isset($staff))
                echo $staff->getName();
            ?>
                            </td>
                        </tr>
        <?php
        }
    }
}
?>
        </tbody> </table>
</div>
<div class="clear"></div>
