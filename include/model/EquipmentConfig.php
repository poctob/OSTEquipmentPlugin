<?php

namespace model;

/**
 * Entity class for equipment configuration
 *
 * @author Alex Pavlunenko 
 */
class EquipmentConfig extends Entity {

    private $id;
    private $key;
    private $value;


    protected function getSaveSQL() {
          $sql = '`key`=' . db_input($this->key) .
                ',`value`=' . db_input($this->value);
        return $sql;
    }

    protected function init() {
        $this->id=0;
        $this->key='undefined';
        $this->value='undefined';        
    }
    public function getId() {
        return $this->id;
    }

    public function getKey() {
        return $this->key;
    }

    public function getValue() {
        return $this->value;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setKey($key) {
        $this->key = $key;
    }

    public function setValue($value) {
        $this->value = $value;
    }

    
    protected static function getIdColumn() {
        return 'id';
    }

    protected static function getTableName() {
        return EQUIPMENT_CONFIG_TABLE;
    }

    public function getJsonProperties() {
        
    }

    protected function validate() {
        $retval = isset($this->key) && $this->key!='undefined';
        if (!$retval) {
            $this->addError('Invalid Key!');            
        }
        return $retval;
    }
    
    public static function findByKey($key)
    {
        if(isset($key))
        {
            $items=static::getAll();
            foreach($items as $item)
            {
                if($item->getKey() == $key)
                {
                    return $item->getValue();
                }
            }
        }
        return null;
    }
    
    public static function saveConfig($key, $value)
    {
        if(isset($key))
        {
            $items=static::getAll();
            foreach($items as $item)
            {
                if($item->getKey() == $key)
                {
                    $item->setValue($value);
                    $item->save();
                }
            }
            $item = new \model\EquipmentConfig();
            $item->setKey($key);
            $item->setValue($value);
            $item->save();
        }
      
    }

   

}
