<?php
/*********************************************************************
    equipment.inc.php
 
    Alex P <alexp@xpresstek.net>
    Copyright (c)  2013 XpressTek
    http://www.xpresstek.net

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
**********************************************************************/
if(!defined('OSTSCPINC') || !$thisstaff) die('Access Denied');
$info=array();
$qstr='';
if($equipment){
    $title='Update equipment: '.$equipment->getName();
    $action='update';
    $submit_text='Save Changes';
    $info=$equipment->getHashtable();
    $info['id']=$equipment->getId();
    $info['status']=$equipment->getStatus();
    $qstr='id='.$equipment->getId();
}else {
    $title='Add New Equipment';
    $action='create';
    $submit_text='Add Equipment';
    if($category) {
        $qstr='cid='.$category->getId();
        $info['category_id']=$category->getId();
    }
}
//TODO: Add attachment support.
$info=Format::htmlchars(($errors && $_POST)?$_POST:$info);
?>
<form action="equipment_item.php?<?php echo $qstr; ?>" method="post" id="save" enctype="multipart/form-data">
 <?php csrf_token(); ?>
 <input type="hidden" name="do" value="<?php echo $action; ?>">
 <input type="hidden" name="a" value="<?php echo Format::htmlchars($_REQUEST['a']); ?>">
 <input type="hidden" name="id" value="<?php echo $info['id']; ?>">
 <h2>Equipment</h2>
 <table class="form_table" width="940" border="0" cellspacing="0" cellpadding="2">
    <thead>
        <tr>
            <th colspan="2">
                <h4><?php echo $title; ?></h4>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <th colspan="2">
                <em>Equipment Information</em>
            </th>
        </tr>
        <tr>
            <td colspan=2>
                <div style="padding-top:3px;"><b>Name</b>&nbsp;<span class="error">*&nbsp;<?php echo $errors['name']; ?></span></div>
                    <input type="text" size="70" name="name" value="<?php echo $info['name']; ?>">
            </td>
        </tr>
          <tr>
            <td colspan=2>
                <div style="padding-top:3px;"><b>Serial Number</b></div>
                    <input type="text" size="70" name="serialnumber" value="<?php echo $info['serialnumber']; ?>">
            </td>
        </tr>
         <tr>
            <th colspan="2">
                <em><strong>Description</strong>: &nbsp;</em>
            </th>
        </tr>
          <tr>
            <td colspan=2>
                <textarea name="description" cols="21" rows="8" style="width: 80%;"><?php echo $info['description']; ?></textarea>
            </td>
        </tr>
        <tr>
            <td colspan=2>
                <div><b>Category Listing</b>:&nbsp;<span class="faded">Equipment category it belongs to.</span></div>
                <select name="category_id" style="width:350px;">
                    <option value="0">Select equipment Category </option>
                    <?php
                    $sql='SELECT category_id, name, ispublic FROM '.EQUIPMENT_CATEGORY_TABLE;
                    if(($res=db_query($sql)) && db_num_rows($res)) {
                        while($row=db_fetch_array($res)) {
                            echo sprintf('<option value="%d" %s>%s (%s)</option>',
                                    $row['category_id'],
                                    (($info['category_id']==$row['category_id'])?'selected="selected"':''),
                                    $row['name'],
                                    ($row['ispublic']?'Public':'Internal'));
                        }
                    }
                   ?>
                </select>
                <span class="error">*&nbsp;<?php echo $errors['category_id']; ?></span>
            </td>
        </tr>
        <tr>
            <td colspan=2>
                <div><b>Listing Type</b>:&nbsp;
                    <span class="faded">Published equipment is listed on public page if the parent category is public.</span></div>
                <input type="radio" name="ispublished" value="1" <?php echo $info['ispublished']?'checked="checked"':''; ?>>Public (publish)
                &nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="ispublished" value="0" <?php echo !$info['ispublished']?'checked="checked"':''; ?>>Internal (private)
                &nbsp;<span class="error">*&nbsp;<?php echo $errors['ispublished']; ?></span>
            </td>
        </tr>
           <tr>
            <td colspan=2>
                <div><b>Status</b>:&nbsp;<span class="faded">Equipment status.</span></div>
                <select name="status_id" style="width:350px;">
                    <option value="0">Select equipment status </option>
                    <?php
                    $sql='SELECT status_id, name, description FROM '.EQUIPMENT_STATUS_TABLE;
                    if(($res=db_query($sql)) && db_num_rows($res)) {
                        while($row=db_fetch_array($res)) {
                            echo sprintf('<option value="%d" %s>%s (%s)</option>',
                                    $row['status_id'],
                                    (($info['status_id']==$row['status_id'])?'selected="selected"':''),
                                    $row['name'],
                                    $row['description']);
                                    
                        }
                    }
                   ?>
                </select>
                <span class="error">*&nbsp;<?php echo $errors['status_id']; ?></span>
            </td>
        </tr>        
        <tr>
            <th colspan="2">
                <em><strong>Internal Notes</strong>: &nbsp;</em>
            </th>
        </tr>
        <tr>
            <td colspan=2>
                <textarea name="notes" cols="21" rows="8" style="width: 80%;"><?php echo $info['notes']; ?></textarea>
            </td>
        </tr>
    </tbody>
</table>
<p style="padding-left:225px;">
    <input type="submit" name="submit" value="<?php echo $submit_text; ?>">
    <input type="reset"  name="reset"  value="Reset">
    <input type="button" name="cancel" value="Cancel" onclick='window.location.href="equipment.php?<?php echo $qstr; ?>"'>
</p>
</form>
