<?php
/*********************************************************************
    equipment_status.inc.php
 
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
if($status && $_REQUEST['a']!='add'){
    $title='Update Status :'.$status->getName();
    $action='update';
    $submit_text='Save Changes';
    $info=$status->getHashtable();
    $info['id']=$status->getId();
    $qstr.='&id='.$status->getId();
}else {
    $title='Add New Status';
    $action='create';
    $submit_text='Add';
    $qstr.='&a='.$_REQUEST['a'];
}
$info=Format::htmlchars(($errors && $_POST)?$_POST:$info);

?>
<form action="equipment_status.php?<?php echo $qstr; ?>" method="post" id="save">
 <?php csrf_token(); ?>
 <input type="hidden" name="do" value="<?php echo $action; ?>">
 <input type="hidden" name="a" value="<?php echo Format::htmlchars($_REQUEST['a']); ?>">
 <input type="hidden" name="id" value="<?php echo $info['id']; ?>">
 <h2>Equipment Status</h2>
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
                <em>Status information:</em>
            </th>
        </tr>  
        <tr>
            <td colspan=2>
                <div style="padding-top:3px;"><b>Status Name</b>:&nbsp;<span class="faded">Short descriptive name.</span></div>
                    <input type="text" size="70" name="name" value="<?php echo $info['name']; ?>">
                    &nbsp;<span class="error">*&nbsp;<?php echo $errors['name']; ?></span>
                <br>
                <div style="padding-top:3px;"><b>Image</b>:&nbsp;<span class="faded">Path to an image to use for this status.</span></div>
                    <input type="text" size="70" name="image" value="<?php echo $info['image']; ?>">                    
                <br>
                <div style="padding-top:3px;"><b>Color</b>:&nbsp;<span class="faded">Status color code</span></div>
                    <input type="text" size="70" name="color" value="<?php echo $info['color']; ?>">                    
                <br>
                <div style="padding-top:3px;"><b>Default</b>:&nbsp;<span class="faded">This is the default status</span></div>            
                  <input type="checkbox" name="baseline" value="1" <?php echo $info['baseline']?'checked="checked"':''; ?>>                 
                 <br>
                <div style="padding-top:5px;">
                    <b>Status Description</b>:&nbsp;<span class="faded">Summary of the status.</span>
                    &nbsp;
                    <font class="error">*&nbsp;<?php echo $errors['description']; ?></font></div>
                    <textarea class="richtext" name="description" cols="21" rows="12" style="width:98%;"><?php echo $info['description']; ?></textarea>
            </td>
        </tr>    
    </tbody>
</table>
<p style="padding-left:225px;">
    <input type="submit" name="submit" value="<?php echo $submit_text; ?>">
    <input type="reset"  name="reset"  value="Reset">
    <input type="button" name="cancel" value="Cancel" onclick='window.location.href="equipment_status.php"'>
</p>
</form>
