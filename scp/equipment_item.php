<?php
/*********************************************************************
    equipment_item.php
 
    Alex P <alexp@xpresstek.net>
    Copyright (c)  2013 XpressTek
    http://www.xpresstek.net

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
**********************************************************************/
require('staff.inc.php');
require_once(EQUIPMENT_INCLUDE_DIR.'class.equipment.php');

$equipment=$category=null;
if($_REQUEST['id'] && !($equipment=Equipment::lookup($_REQUEST['id'])))
   $errors['err']='Unknown or invalid equipment';

if($_REQUEST['cid'] && !$equipment && !($category=Equipment_Category::lookup($_REQUEST['cid'])))
    $errors['err']='Unknown or invalid equipment category';

if($_POST):
    $errors=array();
    switch(strtolower($_POST['do'])) {
        case 'create':
        case 'add':
            if(($equipment=Equipment::add($_POST,$errors)))
                $msg='Equipment added successfully';
            elseif(!$errors['err'])
                $errors['err'] = 'Unable to add Equipment. Try again!';
        break;
        case 'update':
        case 'edit';
            if(!$equipment)
                $errors['err'] = 'Invalid or unknown Equipment';
            elseif($equipment->update($_POST,$errors)) {
                $msg='Equipment updated successfully';
                $_REQUEST['a']=null; //Go back to view
                $equipment->reload();
            } elseif(!$errors['err'])
                $errors['err'] = 'Unable to update Equipment. Try again!';     
            break;
        case 'manage-equipment':
            if(!$equipment) {
                $errors['err']='Unknown or invalid Equipment';
            } else {
                switch(strtolower($_POST['a'])) {
                    case 'edit':
                        $_GET['a']='edit';
                        break;
                    case 'publish';
                        if($equipment->publish())
                            $msg='Equipment published successfully';
                        else
                            $errors['err']='Unable to publish the Equipment. Try editing it.';
                        break;
                        
                    case 'retire';
                        if($equipment->retire())
                            $msg='Equipment retired successfully';
                        else
                            $errors['err']='Unable to retire the Equipment!';
                        break;
                        
                    case 'activate';
                        if($equipment->activate())
                            $msg='Equipment activated successfully';
                        else
                            $errors['err']='Unable to activate the Equipment!';
                        break;
                        
                    case 'unpublish';
                        if($equipment->unpublish())
                            $msg='Equipment unpublished successfully';
                        else
                            $errors['err']='Unable to unpublish the Equipment. Try editing it.';
                        break;
                  /*  case 'delete':
                        $category = $equipment->getCategory();
                        if($equipment->delete()) {
                            $msg='Equipment deleted successfully';
                            $equipment=null;
                        } else {
                            $errors['err']='Unable to delete Equipment. Try again';
                        }
                        break;*/
                    default:
                        $errors['err']='Invalid action';
                }
            }
            break;
        default:
            $errors['err']='Unknown action';
    
    }
endif;


$inc='equipment_categories.inc.php'; //Equipment landing page.
if($equipment) {
    $inc='equipment_view.inc.php';
    if($_REQUEST['a']=='edit')
        $inc='equipment.inc.php';
}elseif($_REQUEST['a']=='add') {
    $inc='equipment.inc.php';
} elseif($category && $_REQUEST['a']!='search') {
    $inc='equipment_categories.inc.php';
}
$nav->setTabActive('equipment');
require_once(STAFFINC_DIR.'header.inc.php');
require_once(EQUIPMENT_STAFFINC_DIR.$inc);
require_once(STAFFINC_DIR.'footer.inc.php');
?>
