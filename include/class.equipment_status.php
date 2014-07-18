<?php
/*********************************************************************
    class.equipment_status.php

    Backend support for equipment status.

    Copyright (c)  2013 XpressTek
    http://www.xpresstek.net

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
**********************************************************************/
require_once('class.equipment.php');

class Equipment_Status {
    var $id;
    var $ht;
    var $name;
    var $image;
    var $num_equipment;
    var $description;
    var $color;
    var $baseline;

    function Equipment_Status($id=0) {
        $this->id=0;
        $this->load($id);
    }

    function load($id) {

        $sql=' SELECT staus.*,count(equipment.equipment_id) as equipments '
            .' FROM '.EQUIPMENT_STATUS_TABLE.' staus '
            .' LEFT JOIN '.EQUIPMENT_TABLE.' equipment ON(equipment.status_id=staus.status_id) '
            .' WHERE staus.status_id='.db_input($id)
            .' GROUP BY staus.status_id';

        if (!($res=db_query($sql)) || !db_num_rows($res)) 
            return false;

        $this->ht = db_fetch_array($res);
        $this->id = $this->ht['status_id'];


        return true;
    }

    function reload() {
        return $this->load($this->getId());
    }

    /* ------------------> Getter methods <--------------------- */
    function getId() { return $this->id; }
    function getName() { return $this->ht['name']; }
    function getImage() {return $this->ht['image'];}
    function getNumEquipment() { return  $this->ht['equipments']; }
    function getDescription() { return $this->ht['description']; }
    function getColor() { return $this->ht['color']; }
    function getBaseline() { return $this->ht['baseline']; }
  
    function getHashtable() { return $this->ht; }
    
    /* ------------------> Setter methods <--------------------- */
    function setName($name) { $this->ht['name']=$name; }
    function setDescription($desc) { $this->ht['description']=$desc; }
    function setImage($image) { $this->ht['image']=$image; }
    function setBaseline($baseline) { $this->ht['baseline']=$baseline; }
    function setColor($color) { $this->ht['color']=$color; }
    

    /* --------------> Database access methods <---------------- */
    function update($vars, &$errors) { 

        if(!$this->save($this->getId(), $vars, $errors))
            return false;

        //TODO: move FAQs if requested.

        $this->reload();

        return true;
    }

    function delete() {
        
        $sql='DELETE FROM '.EQUIPMENT_STATUS_TABLE
            .' WHERE status_id='.db_input($this->getId())
            .' LIMIT 1';
        if(db_query($sql) && ($num=db_affected_rows())) {
            db_query('DELETE FROM '.EQUIPMENT_TABLE
                    .' WHERE status_id='.db_input($this->getId()));
    
        }

        return $num;
    }

    /* ------------------> Static methods <--------------------- */

    function lookup($id) {
        return ($id && is_numeric($id) && ($c = new Equipment_Status($id)))?$c:null;
    }

    function findIdByName($name) {
        $sql='SELECT status_id FROM '.EQUIPMENT_STATUS_TABLE.' WHERE name='.db_input($name);
        list($id) = db_fetch_row(db_query($sql));

        return $id;
    }

    function findByName($name) {
        if(($id=self::findIdByName($name)))
            return new Equipment_Status($id);

        return false;
    }

    function validate($vars, &$errors) {
         return self::save(0, $vars, $errors,true);
    }

    function create($vars, &$errors) {
        return self::save(0, $vars, $errors);
    }

    public static function getStatusList($non_baseline=false) {

        $status_list=array();
        $sql='SELECT status_id, name '
            .' FROM '.EQUIPMENT_STATUS_TABLE;
        
          if($non_baseline)
            $sql.=' WHERE baseline=0';

        $sql.=' ORDER BY name';

        if(($res=db_query($sql)) && db_num_rows($res))
            while(list($id, $name)=db_fetch_row($res))
                $status_list[$id]=$name;

        return $status_list;
    }
    
        public static function getAll() {
        $statuses = array();
        $sql = 'SELECT status_id ' .
                ' FROM ' . EQUIPMENT_STATUS_TABLE;

        $res = db_query($sql);
        if ($res && ($num = db_num_rows($res))) {
            while ($row = db_fetch_array($res)) {
                $status = new Equipment_Status($row['status_id']);
                $statuses[] = $status->getHashtable();
            }
        }

        return $statuses;
    }
    
     function getBaselineStatus() {
        $sql='SELECT status_id '
            .' FROM '.EQUIPMENT_STATUS_TABLE;
        $sql.=' WHERE baseline=1';
        $sql.=' ORDER BY name';
        $sql.=' LIMIT 1';
        
        list($id) =db_fetch_row(db_query($sql));

        if($id)
            return self::lookup($id);

        return false;
    }
    
    function save($id, $vars, &$errors, $validation=false) {

        //Cleanup.
        $vars['name']=Format::striptags(trim($vars['name']));
      
        //validate
        if($id && $id!=$vars['id'])
            $errors['err']='Internal error. Try again';
      
        if(!$vars['name'])
            $errors['name']='Status name is required';
        elseif(strlen($vars['name'])<3)
            $errors['name']='Name is too short. 3 chars minimum';
        elseif(($cid=self::findIdByName($vars['name'])) && $cid!=$id)
            $errors['name']='Status already exists';

        if(!$vars['description'])
            $errors['description']='Status description is required';

        if($errors) return false;

        /* validation only */
        if($validation) return true;

        //save
        $sql=' name='.db_input($vars['name']).
             ',description='.db_input(Format::safe_html($vars['description'])).
             ',image='.db_input(Format::safe_html($vars['image'])).
             ',color='.db_input(Format::safe_html($vars['color'])).
             ',baseline='.db_input(isset($vars['baseline'])?1:0);
            

        if($id) {
            $presql='UPDATE '.LIST_ITEM_TABLE.' SET value='.db_input($vars['name'])
                .'WHERE properties='.db_input($id)
            .' LIMIT 1';
            db_query($presql);
            
            $sql='UPDATE '.EQUIPMENT_STATUS_TABLE.' SET '.$sql.' WHERE status_id='.db_input($id);
            if(db_query($sql))
                return true;

            $errors['err']='Unable to update Equipment Status.';

        } else {
         
            $sql='INSERT INTO '.EQUIPMENT_STATUS_TABLE.' SET '.$sql;
            if(db_query($sql) && ($id=db_insert_id()))
                   
            return $id;

            $errors['err']='Unable to create Equipment Status. Internal error';
        }

        return false;
    }
    
    public static function getEquipment($status_id)
    {
        $equipment = array();
         $sql = ' SELECT equipment.equipment_id '
                . ' FROM ' . EQUIPMENT_STATUS_TABLE . ' status '
                . ' LEFT JOIN ' . EQUIPMENT_TABLE . ' equipment ON(equipment.status_id=status.status_id) '
                . ' WHERE status.status_id=' . db_input($status_id)
                ;
        $res = db_query($sql);
        
         if ($res && ($num = db_num_rows($res))) {
            while ($row = db_fetch_array($res)) {
                $item = new Equipment($row['equipment_id']);
                $equipment[] = $item;
            }
        }

        return $equipment;
    }
}
?>
