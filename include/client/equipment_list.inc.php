<?php
/*********************************************************************
    equipment_list.inc.php

    Displays a list of equipment
 
    Alex P <alexp@xpresstek.net>
    Copyright (c)  2013 XpressTek
    http://www.xpresstek.net

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
**********************************************************************/
if(!defined('OSTCLIENTINC')) die('Access Denied');

?>
<h1>Equipment Status</h1>
<form action="index.php" method="get" id="equipment-search">
    <input type="hidden" name="a" value="search">
    <div>
        <input id="query" type="text" size="20" name="q" value="<?php echo Format::htmlchars($_REQUEST['q']); ?>">
        <select name="cid" id="cid">
            <option value="">&mdash; All Categories &mdash;</option>
            <?php
            $sql='SELECT cat.category_id, cat.name, count(equipment.category_id) as equipments '
                .' FROM '.EQUIPMENT_CATEGORY_TABLE.' cat '
                .' LEFT JOIN '.EQUIPMENT_TABLE.' equipment USING(category_id) '
                .' WHERE cat.ispublic=1 AND equipment.ispublished=1 '
                .' GROUP BY cat.category_id '
                .' HAVING equipments>0 '
                .' ORDER BY cat.name DESC ';
            if(($res=db_query($sql)) && db_num_rows($res)) {
                while($row=db_fetch_array($res))
                    echo sprintf('<option value="%d" %s>%s (%d)</option>',
                            $row['category_id'],
                            ($_REQUEST['cid'] && $row['category_id']==$_REQUEST['cid']?'selected="selected"':''),
                            $row['name'],
                            $row['equipments']);
            }
            ?>
        </select>
        <input id="searchSubmit" type="submit" value="Search">
    </div>
    <div>
        <select name="status_id" id="status_id">
            <option value="">&mdash; Any Status &mdash;</option>
            <?php
            $sql='SELECT ht.status_id as statusID, ht.name as statusName, count(equipment.status_id) as equipments '
                .' FROM '.EQUIPMENT_STATUS_TABLE.' ht '
                .' LEFT JOIN '.EQUIPMENT_TABLE.' equipment ON(equipment.status_id=ht.status_id) '
                .' GROUP BY ht.status_id '
                .' HAVING equipments>0 '
                .' ORDER BY ht.status_id ';
            if(($res=db_query($sql)) && db_num_rows($res)) {
                while($row=db_fetch_array($res))
                    echo sprintf('<option value="%d" >%s (%d)</option>',
                            $row['statusID'],
                            $row['statusName'],
                             $row['equipments']);
            }
            ?>
        </select>
    </div>
</form>
<hr>
<div>
<?php
if($_REQUEST['q'] || $_REQUEST['cid'] || $_REQUEST['status_id']) { //Search.
    $sql='SELECT equipment.equipment_id, equipment.name '
        .' FROM '.EQUIPMENT_TABLE.' equipment '
        .' LEFT JOIN '.EQUIPMENT_CATEGORY_TABLE.' cat ON(cat.category_id=equipment.category_id) '
        .' LEFT JOIN '.EQUIPMENT_STATUS_TABLE.' ft ON(ft.status_id=equipment.status_id) '
        .' WHERE equipment.ispublished=1 AND cat.ispublic=1';
    
    if($_REQUEST['cid'])
        $sql.=' AND equipment.category_id='.db_input($_REQUEST['cid']);
    
    if($_REQUEST['status_id'])
        $sql.=' AND ft.status_id='.db_input($_REQUEST['status_id']);


    if($_REQUEST['q']) {
        $sql.=" AND equipment.name LIKE ('%".db_input($_REQUEST['q'],false)."%') 
                 OR equipment.serialnumber LIKE ('%".db_input($_REQUEST['q'],false)."%') 
                 OR equipment.description LIKE ('%".db_input($_REQUEST['q'],false)."%')";
    }

    $sql.=' GROUP BY equipment.equipment_id';

    echo "<div><strong>Search Results</strong></div><div class='clear'></div>";
    if(($res=db_query($sql)) && ($num=db_num_rows($res))) {
        echo '<div id="equipment">'.$num.' Equipment matched your search criteria.
                <ol>';
        while($row=db_fetch_array($res)) {
            echo sprintf('
                <li><a href="equipment.php?id=%d" class="previewequipment">%s</a></li>',
                $row['equipment_id'],$row['name'],$row['ispublished']?'Published':'Internal');
        }
        echo '  </ol>
             </div>';
    } else {
        echo '<strong class="faded">The search did not match any items.</strong>';
    }
} else { //Category Listing.
    $sql='SELECT cat.category_id, cat.name, cat.description, cat.ispublic, count(equipment.equipment_id) as equipments '
        .' FROM '.EQUIPMENT_CATEGORY_TABLE.' cat '
        .' LEFT JOIN '.EQUIPMENT_TABLE.' equipment ON(equipment.category_id=cat.category_id AND equipment.ispublished=1) '
        .' WHERE cat.ispublic=1 '
        .' GROUP BY cat.category_id '
        .' HAVING equipments>0 '
        .' ORDER BY cat.name';
    if(($res=db_query($sql)) && db_num_rows($res)) {
        echo '<div>Click on the category to browse equipment.</div>
                <ul id="equipment">';
        while($row=db_fetch_array($res)) {

            echo sprintf('
                <li>
                    <i></i>
                    <h4><a href="equipment.php?cid=%d">%s (%d)</a></h4>
                    %s
                </li>',$row['category_id'],
                Format::htmlchars($row['name']),$row['equipments'],
                Format::safe_html($row['description']));
        }
        echo '</ul>';
        
         $sql='SELECT status.status_id, status.name, status.color as color, count(equipment.equipment_id) as equipments '
        .' FROM '.EQUIPMENT_STATUS_TABLE.' status '
        .' LEFT JOIN '.EQUIPMENT_TABLE.' equipment ON(equipment.status_id=status.status_id AND equipment.ispublished=1) '
        .' GROUP BY status.status_id'
        .' HAVING equipments>0 '
        .' ORDER BY status.name';
    if(($res=db_query($sql)) && db_num_rows($res)) {
        echo '<div>Click on the status to browse equipment.</div>
                <ul id="equipment_status">';
        while($row=db_fetch_array($res)) {

            echo sprintf('
                <li>
                    <i></i>
                    <h4><a href="equipment.php?status=%d" %s>%s (%d)</a></h4>
                    
                </li>',
                   
                    $row['status_id'],
                     'style="color:'.$row['color'].'"',
                Format::htmlchars($row['name']),$row['equipments']);
                
        }
        echo '</ul>';
    } 
    }
    else {
        echo 'NO items found';
    }
}
?>
</div>