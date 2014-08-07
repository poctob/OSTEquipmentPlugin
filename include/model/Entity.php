<?php
namespace model;

/**
 * Superclass for database entities.  Defines some common functionality.
 */
abstract class Entity {

    private $errors;

    protected abstract function init();
    
    public abstract function getJsonProperties();    

    protected abstract function getId();

    protected abstract function setId($id);

    protected abstract function validate();

    protected abstract function getSaveSQL();

    protected abstract static function getTableName();

    protected abstract static function getIdColumn();

    public function __construct($id = 0) {
        $this->init();
        $this->errors = array();
        if ($id > 0) {
            $this->load($id);
        }
    }

    public function delete() {
        $table = static::getTableName();
        $id_column = static::getIdColumn();
        $sql = 'DELETE FROM ' . $table
                . ' WHERE ' . $id_column . '=' . db_input($this->getId())
                . ' LIMIT 1';
        if (db_query($sql) && ($num = db_affected_rows())) {
            return $num;
        }
        $this->addError('Error deleting item!');
        return false;
    }

    private function load($id) {
        $table = static::getTableName();
        $id_column = static::getIdColumn();

        $sql = ' SELECT * '
                . ' FROM ' . $table
                . ' WHERE ' . $id_column . '=' . db_input($id);

        if (!($res = db_query($sql)) || !db_num_rows($res)) {
            $this->addError('Error loading item!');
            return false;
        }

        $cols = db_fetch_array($res);

        foreach ($cols as $key => $value) {
            $func = 'set'.ucfirst($key);
            
            if(method_exists($this, $func))
            {
                $this->$func($value);
            }
        }

        return true;
    }

    protected function reload() {
        return $this->load($this->getId());
    }

    public function save() {
        $retval = false;
        $this->clearErrors();

        if (!$this->validate()) {
            return false;
        }

        $sql = $this->getSaveSQL();
        $id = $this->getId();
        $table = self::getTableName();
        $id_column = self::getIdColumn();

        if ($id > 0) {
            $sql = 'UPDATE ' . $table .
                    ' SET ' . $sql . ' WHERE ' . $id_column . '=' . db_input($this->id);
            $retval = db_query($sql);
        } else {
            $sql = 'INSERT INTO ' . $table .
                    ' SET ' . $sql;
            $retval = db_query($sql);
            if ($retval) {
                $this->setId(db_insert_id());
            }
        }
        if (!$retval) {
            $this->addError('Error saving item!');
        }
        return $retval;
    }

    public function getErrors() {
        return $this->errors;
    }

    protected function clearErrors() {
        unset($this->errors);
        $this->errors = array();
    }

    protected function addError($error) {
        $this->errors[] = $error;
    }

    public static function getAll() {

        $table = static::getTableName();
        $id_column = static::getIdColumn();
        $sql = 'SELECT ' . $id_column
                . ' FROM ' . $table;

        return static::populateBySQL($sql);
    }

    public static function populateBySQL($sql) {
        $id_column = static::getIdColumn();
        $items = array();
        $res = db_query($sql);
        if ($res && ($num = db_num_rows($res))) {
            while ($row = db_fetch_array($res)) {
                $item = new static($row[$id_column]);
                $items[] = $item;
            }
        }
        return $items;
    }

    public static function getById($id) {
        return new self($id);
    }

}
