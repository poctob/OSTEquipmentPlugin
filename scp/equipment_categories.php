<?php
/*********************************************************************
    equipment_categories.php
 
    Alex P <alexp@xpresstek.net>
    Copyright (c)  2013 XpressTek
    http://www.xpresstek.net

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
**********************************************************************/
require('staff.inc.php');
include_once(EQUIPMENT_INCLUDE_DIR.'class.equipment_category.php');


/* check permission */
if(!$thisstaff) {
    header('Location: equipment.php');
    exit;
}

$category=null;
$tickets_status=null;
if($_REQUEST['id'] && !($category=Equipment_Category::lookup($_REQUEST['id'])))
    $errors['err']='Unknown or invalid category ID.';

$tickets_status=$_REQUEST['tickets'];

if($_POST){
    switch(strtolower($_POST['do'])) {
        case 'update':
            if(!$category) {
                $errors['err']='Unknown or invalid category.';
            } elseif($category->update($_POST,$errors)) {
                $msg='Category updated successfully';
            } elseif(!$errors['err']) {
                $errors['err']='Error updating category. Try again!';
            }
            break;
        case 'create':
            if(($id=Equipment_Category::create($_POST,$errors))) {
                $msg='Equipment_Category added successfully';
                $_REQUEST['a']=null;
            } elseif(!$errors['err']) {
                $errors['err']='Unable to add category. Correct error(s) below and try again.';
            }
            break;
        case 'mass_process':
            if(!$_POST['ids'] || !is_array($_POST['ids']) || !count($_POST['ids'])) {
                $errors['err']='You must select at least one category';
            } else {
                $count=count($_POST['ids']);
                switch(strtolower($_POST['a'])) {
                    case 'make_public':
                        $sql='UPDATE '.EQUIPMENT_CATEGORY_TABLE.' SET ispublic=1 '
                            .' WHERE category_id IN ('.implode(',', db_input($_POST['ids'])).')';
                    
                        if(db_query($sql) && ($num=db_affected_rows())) {
                            if($num==$count)
                                $msg = 'Selected categories made PUBLIC';
                            else
                                $warn = "$num of $count selected categories made PUBLIC";
                        } else {
                            $errors['err'] = 'Unable to enable selected categories public.';
                        }
                        break;
                    case 'make_private':
                        $sql='UPDATE '.EQUIPMENT_CATEGORY_TABLE.' SET ispublic=0 '
                            .' WHERE category_id IN ('.implode(',', db_input($_POST['ids'])).')';

                        if(db_query($sql) && ($num=db_affected_rows())) {
                            if($num==$count)
                                $msg = 'Selected categories made PRIVATE';
                            else
                                $warn = "$num of $count selected categories made PRIVATE";
                        } else {
                            $errors['err'] = 'Unable to disable selected categories PRIVATE';
                        }
                        break;
                    case 'delete':
                        $i=0;
                        foreach($_POST['ids'] as $k=>$v) {
                            if(($c=Equipment_Category::lookup($v)) && $c->delete())
                                $i++;
                        }

                        if($i==$count)
                            $msg = 'Selected Equipment Category deleted successfully';
                        elseif($i>0)
                            $warn = "$i of $count selected categories deleted";
                        elseif(!$errors['err'])
                            $errors['err'] = 'Unable to delete selected Equipment Category';
                        break;
                    default:
                        $errors['err']='Unknown action/command';
                }
            }
            break;
        default:
            $errors['err']='Unknown action';
            break;
    }
}

$page='equipment_categories.inc.php';
if(isset($category) && isset($tickets_status))
{
    $page='equipment_category_tickets.inc.php';
}
else if($category || ($_REQUEST['a'] && !strcasecmp($_REQUEST['a'],'add')))
{
    $page='equipment_category.inc.php';
}
 
$nav->setTabActive('equipment');
require(STAFFINC_DIR.'header.inc.php');
require(EQUIPMENT_STAFFINC_DIR.$page);
require(STAFFINC_DIR.'footer.inc.php');
?>
