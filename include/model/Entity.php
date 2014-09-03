<?php
namespace model;

/**
 * Superclass for database entities.  Defines some common functionality.
 */
abstract class Entity {

    private $errors;

    /**
     * Called to initialize data object.
     */
    protected abstract function init();
    
    /**
     * Serializes class properties into JSON object.
     */
    public abstract function getJsonProperties();    

    /**
     * Primary key getter.
     */
    protected abstract function getId();

    /**
     * Privary key setter.
     */
    protected abstract function setId($id);

    /**
     * Validates class data before inserting or updating in the database.
     */
    protected abstract function validate();

    /**
     * Generates SQL column / data mappings.
     */
    protected abstract function getSaveSQL();

    /**
     * Table name that corresponds to this class.
     */
    protected abstract static function getTableName();

    /**
     * ID column name for this class.
     */
    protected abstract static function getIdColumn();

    /**
     * Default constructor.  Generates new data object.
     * @param type $id Primary key.
     */
    public function __construct($id = 0) {
        $this->init();
        $this->errors = array();
        if ($id > 0) {
            $this->load($id);
        }
    }

    /**
     * Deletes object from the database.
     * @return boolean True if object was deleted successfully, false otherwise.
     */
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

    /**
     * Loads object's data from the database using primary key.
     * @param type $id Primary key.
     * @return boolean Result of the operation.
     */
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

    /**
     * Refreshes object's data using database.
     * @return type
     */
    protected function reload() {
        return $this->load($this->getId());
    }
    
    /**
     * Saves populates object with provided data and saves it in the database.
     * @param type $data Data to use.
     * @return type
     */
    public function saveFromData($data)
    {
        foreach($data as $key => $value)
        {
             $func = 'set'.ucfirst($key);
            
            if(method_exists($this, $func))
            {
                $this->$func($value);
            }
        }
        return $this->save();
    }

    /**
     * Saves object in the database (insert or update)
     * @return boolean Operation result.
     */
    public function save() {
        $retval = false;
        $this->clearErrors();

        if (!$this->validate()) {
            return false;
        }

        $sql = $this->getSaveSQL();
        $id = $this->getId();
        $table = static::getTableName();
        $id_column = static::getIdColumn();

        if ($id > 0) {
            $sql = 'UPDATE ' . $table .
                    ' SET ' . $sql . ' WHERE ' . $id_column . '=' . db_input($id);
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
    
    /**
     * Provides additional functionality that may be extended after save
     * operation.
     * @param type $data Data that was saved.
     */
    public function postSave($data)
    {
        
    }
   
    /**
     * Errors getter.
     * @return type
     */
    public function getErrors() {
        return $this->errors;
    }

    /**
     * Clears all errors from local store.
     */
    protected function clearErrors() {
        unset($this->errors);
        $this->errors = array();
    }

    /**
     * Adds an error to local store.
     * @param type $error Error to add
     */
    protected function addError($error) {
        $this->errors[] = $error;
    }

    /**
     * Gets all records for current type.
     * @return type
     */
    public static function getAll() {

        $table = static::getTableName();
        $id_column = static::getIdColumn();
        $sql = 'SELECT ' . $id_column
                . ' FROM ' . $table;

        return static::populateBySQL($sql);
    }

    /**
     * Populates object from provides SQL statement.
     * @param type $sql SQL statement to use in query.
     * @return \static
     */
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

    /**
     * Fetches an object using its primary key.
     * @param type $id Primary key to use.
     * @return \self
     */
    public static function getById($id) {
        return new self($id);
    }

}
