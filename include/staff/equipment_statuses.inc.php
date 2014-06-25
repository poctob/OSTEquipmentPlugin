<?php
/*********************************************************************
    equipment_statuses.inc.php
 
    Alex P <alexp@xpresstek.net>
    Copyright (c)  2013 XpressTek
    http://www.xpresstek.net

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
**********************************************************************/
if(!defined('OSTSCPINC') || !$thisstaff) die('Access Denied');

$qstr='';
$sql='SELECT status.status_id, status.name, status.color as color, status.baseline as baseline,
    status.description, count(equipment.equipment_id) as equipments '.
     ' FROM '.EQUIPMENT_STATUS_TABLE.' status '.
     ' LEFT JOIN '.EQUIPMENT_TABLE.' equipment ON (equipment.status_id=status.status_id) ';
$sql.=' WHERE 1';

$sortOptions=array('name'=>'status.name','equipments'=>'equipments');
$orderWays=array('DESC'=>'DESC','ASC'=>'ASC');
$sort=($_REQUEST['sort'] && $sortOptions[strtolower($_REQUEST['sort'])])?strtolower($_REQUEST['sort']):'name';
//Sorting options...
if($sort && $sortOptions[$sort]) {
    $order_column =$sortOptions[$sort];
}
$order_column=$order_column?$order_column:'status.name';

if($_REQUEST['order'] && $orderWays[strtoupper($_REQUEST['order'])]) {
    $order=$orderWays[strtoupper($_REQUEST['order'])];
}
$order=$order?$order:'ASC';

if($order_column && strpos($order_column,',')){
    $order_column=str_replace(','," $order,",$order_column);
}
$x=$sort.'_sort';
$$x=' class="'.strtolower($order).'" ';
$order_by="$order_column $order ";

$total=db_count('SELECT count(*) FROM '.EQUIPMENT_STATUS_TABLE.' status ');
$page=($_GET['p'] && is_numeric($_GET['p']))?$_GET['p']:1;
$pageNav=new Pagenate($total, $page, PAGE_LIMIT);
$pageNav->setURL('equipment_status.php',$qstr.'&sort='.urlencode($_REQUEST['sort']).'&order='.urlencode($_REQUEST['order']));
$qstr.='&order='.($order=='DESC'?'ASC':'DESC');
$query="$sql GROUP BY status.status_id ORDER BY $order_by LIMIT ".$pageNav->getStart().",".$pageNav->getLimit();
$res=db_query($query);
if($res && ($num=db_num_rows($res)))
    $showing=$pageNav->showing().' status';
else
    $showing='No Equipment status found!';

?>
<div style="width:700;padding-top:5px; float:left;">
 <h2>Equipment Status</h2>
 </div>
<div style="float:right;text-align:right;padding-top:5px;padding-right:5px;">
    <b><a href="equipment_status.php?a=add" class="Icon newCategory">Add New Status</a></b></div>
<div class="clear"></div>
<form action="equipment_status.php" method="POST" name="cat">
 <?php csrf_token(); ?>
 <input type="hidden" name="do" value="mass_process" >
 <input type="hidden" id="action" name="a" value="" >
 <table class="list" border="0" cellspacing="1" cellpadding="0" width="940">
    <caption><?php echo $showing; ?></caption>
    <thead>
        <tr>
            <th width="7">&nbsp;</th>
            <th width="500"><a <?php echo $name_sort; ?> href="equipment_status.php?<?php echo $qstr; ?>&sort=name">Name</a></th>           
            <th width="80"><a  <?php echo $equipments_sort; ?> href="equipment_status.php?<?php echo $qstr; ?>&sort=equipment">Equipment</a></th>
           
        </tr>
    </thead>
    <tbody>
    <?php
        $total=0;
        $ids=($errors && is_array($_POST['ids']))?$_POST['ids']:null;
        if($res && db_num_rows($res)):
            while ($row = db_fetch_array($res)) {
                $sel=false;
                if($ids && in_array($row['status_id'],$ids))
                    $sel=true;
                
                $equipments=0;
                if($row['equipments'])
                    $equipments=sprintf('<a href="equipment.php?status_id=%d">%d</a>',$row['status_id'],$row['equipments']);
                ?>
            <tr id="<?php echo $row['status_id']; ?>">
                <td width=7px>
                  <input type="checkbox" name="ids[]" value="<?php echo $row['status_id']; ?>" class="ckb"
                            <?php echo $sel?'checked="checked"':''; ?>>
                </td>
                <td><a href="equipment_status.php?id=<?php echo $row['status_id']; ?>" style="color:<?php echo $row['color']?> !important;"><?php echo Format::truncate($row['name'],200); ?></a>&nbsp;</td>                
                <td style="text-align:right;padding-right:25px;"><?php echo $equipments; ?></td>
                <td>&nbsp;<?php echo Format::db_datetime($row['updated']); ?></td>
            </tr>
            <?php
            } //end of while.
        endif; ?>
    <tfoot>
     <tr>
        <td colspan="5">
            <?php if($res && $num){ ?>
            Select:&nbsp;
            <a id="selectAll" href="#ckb">All</a>&nbsp;&nbsp;
            <a id="selectNone" href="#ckb">None</a>&nbsp;&nbsp;
            <a id="selectToggle" href="#ckb">Toggle</a>&nbsp;&nbsp;
            <?php }else{
                echo 'No Equipment status found.';
            } ?>
        </td>
     </tr>
    </tfoot>
</table>
<?php
if($res && $num): //Show options..
    echo '<div>&nbsp;Page:'.$pageNav->getPageLinks().'&nbsp;</div>';
?>
<p class="centered" id="actions">
    <input class="button" type="submit" name="make_public" value="Make Public">
    <input class="button" type="submit" name="make_private" value="Make Internal">
    <input class="button" type="submit" name="delete" value="Delete" >
</p>
<?php
endif;
?>
</form>
<div style="display:none;" class="dialog" id="confirm-action">
    <h3>Please Confirm</h3>
    <a class="close" href="">&times;</a>
    <hr/>
 
    <p class="confirm-action" style="display:none;" id="delete-confirm">
        <font color="red"><strong>Are you sure you want to DELETE selected status?</strong></font>
        <br><br>Deleted entries CANNOT be recovered, including any associated equipment.
    </p>
    <div>Please confirm to continue.</div>
    <hr style="margin-top:1em"/>
    <p class="full-width">
        <span class="buttons" style="float:left">
            <input type="button" value="No, Cancel" class="close">
        </span>
        <span class="buttons" style="float:right">
            <input type="button" value="Yes, Do it!" class="confirm">
        </span>
     </p>
    <div class="clear"></div>
</div>
