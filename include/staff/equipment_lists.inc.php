<?php
/*********************************************************************
    equipment_lists.inc.php
 
    Alex P <alexp@xpresstek.net>
    Copyright (c)  2013 XpressTek
    http://www.xpresstek.net

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
**********************************************************************/
if(!defined('OSTSTAFFINC') || !$thisstaff) die('Access Denied');

?>
<h2>Equipment</h2>
<form id="kbSearch" action="equipment.php" method="get">
    <input type="hidden" name="a" value="search">
    <div>
        <input id="query" type="text" size="20" name="q" value="<?php echo Format::htmlchars($_REQUEST['q']); ?>">
        <select name="cid" id="cid">
            <option value="">&mdash; All Equipment Categories &mdash;</option>
            <?php
            $sql='SELECT category_id, cat.name, count(equipment.category_id) as equipments '
                .' FROM '.EQUIPMENT_CATEGORY_TABLE.' cat '
                .' LEFT JOIN '.EQUIPMENT_TABLE.' equipment USING(category_id) '
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
        <select name="status_id" style="width:350px;" id="status_id">
            <option value="">&mdash; All Equipment Status &mdash;</option>
            <?php
            $sql='SELECT status.status_id as StatusID, status.name as Status, count(equipment.status_id) as equipments '
                .' FROM '.EQUIPMENT_STATUS_TABLE.' status '
                .' LEFT JOIN '.EQUIPMENT_TABLE.' equipment ON(equipment.status_id=status.status_id) '
                .' GROUP BY status.name '
                .' HAVING equipments>0 ';
            if(($res=db_query($sql)) && db_num_rows($res)) {
                while($row=db_fetch_array($res))
                    echo sprintf('<option value="%d">%s (%d)</option>',
                            $row['StatusID'],
                            $row['Status'], 
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
    $sql='SELECT equipment.equipment_id, equipment.name, equipment.ispublished '
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
    if(($res=db_query($sql)) && db_num_rows($res)) {
        echo '<div id="equipment">
                <ol>';
        while($row=db_fetch_array($res)) {
            echo sprintf('
                <li><a href="equipment.php?id=%d" class="previewequipment">%s</a> - <span>%s</span></li>',
                $row['equipment_id'],$row['name'],$row['ispublished']?'Published':'Internal');
        }
        echo '  </ol>
             </div>';
    } else {
        echo '<strong class="faded">The search did not match any equipment.</strong>';
    }
} else { //Category Listing.
    $sql='SELECT cat.category_id, cat.name, cat.description, 
         cat.ispublic, count(equipment.equipment_id) as equipments '
        .' FROM '.EQUIPMENT_CATEGORY_TABLE.' cat '
        .' LEFT JOIN '.EQUIPMENT_TABLE.' equipment ON(equipment.category_id=cat.category_id) '
        .' GROUP BY cat.category_id '
        .' ORDER BY cat.name';
    if(($res=db_query($sql)) && db_num_rows($res)) {
        echo '<div>Click on the category to browse equipment.</div>
                <ul id="equipment">';
        while($row=db_fetch_array($res)) {

            echo sprintf('
                <li>
                    <h4><a href="equipment.php?cid=%d">%s (%d)</a> - <span>%s</span></h4>
                    %s
                </li>',$row['category_id'],$row['name'],$row['equipments'],
                ($row['ispublic']?'Public':'Internal'),
                Format::safe_html($row['description']));
        }
        echo '</ul>';
    } else {
        echo 'NO equipment found';
    }
}
?>
</div>
