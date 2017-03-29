<?php

/**
 * Magic Database object Subclasses should either
 * have the same name as the DB table, or need to
 * override getDbTableName() function.
 *
 * All properties need to have the same name as
 * DB table properties or need to be considered
 * in getDbColumnName() function.
 *
 * A subclass can define the following special
 * properties for special handling:
 *
 * 1. _* properties are considered transient not automatically
 *    saved to DB
 *
 * 2. dt_* any property starting with dt_ will be transformed
 *    into seconds when loaded from DB and turned back into
 *    MySQL datetime format when saved to DB.
 *
 * 3. d_* as above but data only
 *
 * 4. t_* as above but time only
 *
 * 5. is_deleted when exists will be set to 1 instead of
 *    deleting the table data on object::delete(), to really
 *    delete the data in the DB ::delete(true) must be called!
 *
 * 6. title when exists will be automatically used for
 *    object::getSelectOptionTitle() method
 *
 * 7. name when exists and title doesn't exist then will be used
 *    for object::getSelectOptionTitle() method
 *
 * 8. Automagic Select UI Field Hints, see getSelectOptions() for more info.
 *    static $_<fieldname>_ui_select_string = array("option1","option2",...);
 *    static $_<fieldname>_ui_select_lookup_code = "states";
 *    static $_<fieldname>_ui_select_objects_class = "Contact";
 *    static $_<fieldname>_ui_select_objects_filter = array("is_deleted"=>0);
 *
 * 9. Define the Database Table Name (optional, see DbObject::getDbTableName()):
 *    public $_db_table = "<table name>";
 *
 * DbObject supports the use of the following 'Aspects' which can be
 * added to any object using a magic '$_<aspect>' property:
 *
 * 1. SearchableAspect -> public $_searchable;
 *    This Aspect does not add any public functions to the object, but extends
 *    the insert/update/delete behaviour so that an index record is created (or updated)
 *    in the table object_index which contains the object_id reference and a sanitised 
 *    string of the content of the source object's fields for fulltext retrieval.
 *    
 *    Per default all properties (except thos in the $_exclude_index array) are concatenated
 *    and included in the index. In order to add custom content (eg. from dependent tables)
 *    create the following:
 *    
 *    function addToIndex() {}
 *    
 *    Which should return a string to be added to the indexable content. All sanitising and
 *    word de-duplication is performed on this.
 *    
 * 2. Aspects can be removed in the case of class inheritance. If the parent class has 
 *    public $_searchable; defined then this can be removed by a child class using:
 *    public $_remove_searchable. However further childclasses can no longer add this aspect!
 *    
 * 3. Auditing of inserts and updates happens automatically to an audit table.
 *    However this can be turned off by setting
 *    public $__use_auditing = false;
 *
 * @author carsten
 *
 */
class DbObject extends DbService {

    public $id;
    private static $_object_vars = array();
	private static $_columns = array();
    private $_class;
	public $__use_auditing = true;
	
    /**
     * Constructor
     *
     * @param $w
     */
    function __construct(Web &$w) {
        parent::__construct($w);

        // add standard aspects
        if (property_exists($this, "_modifiable") && !property_exists($this, "_remove_modifiable")) {
            $this->_modifiable = new AspectModifiable($this);
        }
        if (property_exists($this, "_versionable") && !property_exists($this, "_remove_versionable")) {
            $this->_versionable = new AspectVersionable($this);
        }
        if (property_exists($this, "_searchable") && !property_exists($this, "_remove_searchable")) {
            $this->_searchable = new AspectSearchable($this);
        }
        $this->_class = get_class($this);
    }

    // public function __clone(){
    // }

    public function __get($name) {
        // cater for modifiable aspect!
        if (isset($this->_modifiable)) {
            if ($name == "dt_created") {
                return $this->_modifiable->getCreatedDate();
            }
            if ($name == "dt_modified") {
                return $this->_modifiable->getModifiedDate();
            }
        }
        if (property_exists($this, $name)) {
            $reflection = new ReflectionProperty($this, $name);
            $reflection->setAccessible($name);
            return $reflection->getValue($this);
        } else {
            return $this->w->$name;
        }
    }

    /**
     * Set a cryptography password for
     * automatic encryption, decryption
     *
     * for 128bit AES choose 16 characters
     * for 192bit AES choose 24 characters
     * for 256bit AES choose 32 characters
     */
    function setPassword($password) {
        if ($password) {
            Config::set('system.password_salt', $password);
        }
    }

    /**
     * decrypt all fields that are marked with
     * a 's_' prefix
     */
    function decrypt() {
        foreach (get_object_vars($this) as $k => $v) {
            if (strpos($k, "s_") === 0) {
                if ($v) {
                    $this->$k = AESdecrypt($v, Config::get('system.password_salt'));
                }
            }
        }
    }

    /**
     *
     * intermediate method to facilitate transition from
     * selectTitle to getSelectOptionTitle
     */
    function _selectOptionTitle() {
        $title = $this->getSelectOptionValue();
        if (property_exists(get_class($this), "title")) {
            $title = $this->title;
        } else if (property_exists(get_class($this), "name")) {
            $title = $this->name;
        }
        return $title;
    }

    /**
     * is used by the Html::select() function to display this object in
     * a select list. Could also be used by other similar functions.
     */
    function getSelectOptionTitle() {
        return $this->_selectOptionTitle(); // only until all references are resolved
    }

    /**
     * This is used by the Html::select() function to retrieve the key/title pairing
     *
     * this should only be overridden, if the id is NOT the key.
     */
    function getSelectOptionValue() {
        return $this->id;
    }

    /**
     * used by the search display function to print a title with a
     * possible link for this item in the list of results.
     */
    function printSearchTitle() {
        return get_class($this) . "[" . $this->id . "]";
    }

    /**
     * used by the search display function to print more information
     * about this item in the list of search results.
     */
    function printSearchListing() {
        return get_class($this) . "[" . $this->id . "]";
    }

    /**
     * used by the search display function to print a url for viewing details
     * about this item.
     */
    function printSearchUrl() {
        return null;
    }

    /**
     * print a view link to this object
     * 
     * @param string $class
     * @param string $target
     * @return string
     */
    function toLink($class = null, $target = null, $user = null) {
        if (empty($user)) {
            $user = $this->w->Auth->user();
        }
        if ($this->canView($user)) {
            return Html::a($this->w->localUrl($this->printSearchUrl()), $this->printSearchTitle(), null, $class, null, $target);
        }
        return $this->printSearchTitle();
    }

    /**
     * used by the search display function to check whether the user has
     * permission to see this result item.
     *
     * @param <type> $user
     * @return <type>
     */
    function canList(User $user) {
        return true;
    }

    /**
     * used by the search display function to check whether the user has
     * permission to view further details about this item.
     *
     * @param User $user
     */
    function canView(User $user) {
        return true;
    }

    /**
     * can be used by other function to check whether the user has
     * permissions to edit this item.
     *
     * @param User $user
     */
    function canEdit(User $user) {
        return true;
    }

    /**
     * can be used by other functions to check whether this user has
     * permissions to delete this item.
     *
     * @param User $user
     */
    function canDelete(User $user) {
        return true;
    }

    /**
     * Apply value conversions from database values
     * 
     * @param string $k
     * @param mixed $v
     * @return mixed
     */
    function readConvert($k, $v) {
        if (strpos($k, "dt_") === 0) {
            if (!empty($v)) {
                return $this->dt2Time($v);
            }
        } else if (strpos($k, "d_") === 0) {
            if (!empty($v)) {
                return $this->d2Time($v);
            }
        } 
        return $v;
    }
    
    function getObjectVars() {
        if(!empty(self::$_object_vars[$this->_class])) {
            return self::$_object_vars[$this->_class];
        }
        // build cache of filtered object vars
        self::$_object_vars[$this->_class] = array();
        foreach(get_object_vars($this) as $k => $v) {
            // ignore volatile vars and web
            if('_' !== $k{0} && 'w' !== $k) {
                self::$_object_vars[$this->_class][] = $k;
            }
        }
        return self::$_object_vars[$this->_class];
    }
    
    /**
     * fill this object from an array where the keys correspond to the
     * variable of this object.
     *
     * @param array $row
     */
    function fill($row, $convert = false) {
        foreach ($this->getObjectVars() as $k) {
            if (array_key_exists($k, $row)) {
                $this->$k = ($convert ? $this->readConvert($k, $row[$k]) : $row[$k]);
            }
        }
        if (!empty($row["creator_id"]) && empty($this->creator_id)) {
            $this->creator_id = $row["creator_id"];
        }
        if (!empty($row["modifier_id"]) && empty($this->modifier_id)) {
            $this->modifier_id = $row["modifier_id"];
        }
        if (!empty($row["is_deleted"]) && empty($this->is_deleted)) {
            $this->is_deleted = $row["is_deleted"];
        }
    }

    /**
     * Creates a shallow copy of an object without saving to DB (by default)
     * 
     * @param boolean saveToDB (optional, default false)
     * @return Object
     */
    public function copy($saveToDB = false) {
        $newObject = clone $this;

        $toClear = array("id", "creator_id", "modifier_id", "dt_created", "dt_modified", "is_deleted");

        foreach ($toClear as $tc) {
            if (property_exists($newObject, $tc)) {
                $newObject->$tc = null;
            }
        }

        if ($saveToDB) {
            // Dont force validation so we dont get errors if data is missing, this is the responsibility of the dev
            $newObject->insert(false);
        }

        return $newObject;
    }

    /**
     * Store all object attributes in
     * an associative array and return this.
     *
     * @return array
     */
    function toArray() {
        $arr = array();
        foreach ($this->getObjectVars() as $k) {
            $arr[$k] = $this->$k;
        }
        return $arr;
    }

    /**
     * Return a human readable formatted date
     *
     * @param <type> $var
     * @param <type> $format
     * @return <type> a formatted date
     */
    function getDate($var, $format = 'd/m/Y') {
        if (array_key_exists($var, get_object_vars($this)) && $this->$var) {
            return $this->time2D($this->$var, $format);
        }
    }

    /**
     *
     * @param <type> $var
     * @param <type> $format
     * @return <type> formatted date and time
     */
    function getDateTime($var, $format = 'd/m/Y H:i') {
        if (array_key_exists($var, get_object_vars($this)) && $this->$var) {
            return $this->time2Dt($this->$var, $format);
        }
    }

    /**
     *
     * @param <type> $var
     * @param <type> $format
     * @return <type> formatted date and time
     */
    function getTime($var, $format = null) {
        if (array_key_exists($var, get_object_vars($this)) && $this->$var) {
            return $this->time2T($this->$var, $format);
        }
    }

    function setTime($var, $date) {
        if (array_key_exists($var, get_object_vars($this))) {
            $this->$var = $this->t2Time($date);
        }
    }

    /**
     * Transform a human readable date into a timestamp to be
     * stored in this object.
     *
     * @param <type> $var
     * @param <type> $date
     */
    function setDate($var, $date) {
        if (array_key_exists($var, get_object_vars($this))) {
            $this->$var = $this->d2Time($date);
        }
    }

    /**
     * Transform a human readable date into a timestamp to be
     * stored in this object.
     *
     * @param <type> $var
     * @param <type> $date
     */
    function setDateTime($var, $date) {
        if (array_key_exists($var, get_object_vars($this))) {
            $this->$var = $this->dt2Time($date);
        }
    }

	/**
	 * Returns whether or not this object exists in the database
	 * Base on the id not being null and greater than 0
	 * 
	 * @return <bool> exists
	 */
	public function exists() {
		return !is_null($this->id) && intval($this->id) > 0;
	}
	
	/**
	 * Checks whether or not a given property has changed. It does this by
	 * looking for the $prop value in the __old array and comparing it against
	 * the active property.
	 * 
	 * @param string $prop
	 * @return boolean
	 */
	public function propertyHasChanged($prop) {
		return property_exists($this, '__old') && property_exists($this, $prop) && 
				array_key_exists($prop, $this->__old) && $this->__old[$prop] != $this->$prop;
	}
	
    /**
     * Utility function to decide
     * whether to insert or update
     * an object in the database.
     */
    function insertOrUpdate($force_null_values = false, $force_validation = true) {
        if ($this->id != null) {
            return $this->update($force_null_values, $force_validation);
        } else {
            return $this->insert($force_validation);
        }
    }

    /**
     * Call database action hooks:
     * 
     * core_dbobject_before_insert
     * core_dbobject_before_insert_[classname]
     * core_dbobject_after_insert
     * core_dbobject_after_insert_[classname]
     * core_dbobject_before_update
     * core_dbobject_before_update_[classname]
     * core_dbobject_after_update
     * core_dbobject_after_update_[classname]
     * core_dbobject_before_delete
     * core_dbobject_before_delete_[classname]
     * core_dbobject_after_delete
     * core_dbobject_after_delete_[classname]
     * 
     * @param unknown $type eg. before / after
     * @param unknown $action eg. insert / update / delete
     */
    private function _callHooks($type, $action) {
        $this->w->callHook("core_dbobject", $type . "_" . $action, $this);
        $this->w->callHook("core_dbobject", $type . "_" . $action . "_" . get_class($this), $this);
    }

    /**
     * create and execute a sql insert statement for this object.
     *
     * @param <type> $table
     * @throws Exception e
     * @return  boolean|array true or Array of validation errors
     */
    function insert($force_validation = true) {
        try {
            $this->startTransaction();

            if ($force_validation && property_exists($this, "_validation")) {
                $valid_response = $this->validate();
                if (!$valid_response ['success']) {
                    $this->rollbackTransaction();
                    return $valid_response;
                }
            }

            // calling hooks BEFORE inserting the object
            $this->_callHooks("before", "insert");

            $t = $this->getDbTableName();
            $columns = $this->getDbTableColumnNames();

            // set some default attributes
            if (!property_exists($this, "_modifiable")) { // $this->_modifiable) {
                // for backwards compatibility
                if (in_array("dt_created", $columns) && empty($this->dt_created)) {
                    $this->dt_created = time();
				}

                if (in_array("creator_id", $columns) && $this->w->Auth->loggedIn()) {
                    $this->creator_id = $this->w->Auth->user()->id;
				}

                if (in_array("dt_modified", $columns)) {
                    $this->dt_modified = time();
				}

                if (in_array("modifier_id", $columns) && $this->w->Auth->loggedIn()) {
                    $this->modifier_id = $this->w->Auth->user()->id;
				}
            }

            $data = array();
            foreach (get_object_vars($this) as $k => $v) {
                if ($k {0} != "_" && $k != "w" && $v !== null) {
                    $dbk = $this->getDbColumnName($k);
                    if (strpos($k, "dt_") === 0) {
                        if ($v) {
                            $v = $this->time2Dt($v);
                            $data [$dbk] = $v;
                        }
                    } else if (strpos($k, "d_") === 0) {
                        if ($v) {
                            $v = $this->time2D($v);
                            $data [$dbk] = $v;
                        }
                    } else if (strpos($k, "t_") === 0) {
                        if ($v) {
                            $v = $this->time2T($v);
                            $data [$dbk] = $v;
                        }
                    } else if (strpos($k, "s_") === 0) {
                        if ($v) {
                            $v = AESencrypt($v, Config::get('system.password_salt'));
                            $data [$dbk] = $v;
                        }
                    } else {
                        $data [$dbk] = $v;
                    }
                }
            }

            $this->_db->insert($t, $data);
            $this->_db->execute();

            $this->id = $this->_db->last_insert_id();

            // calling hooks AFTER inserting the object
            $this->_callHooks("after", "insert");

            // call standard aspect methods

            if (property_exists($this, "_modifiable") && (null !== $this->_modifiable)) {
                $this->_modifiable->insert();
            }
            if (property_exists($this, "_versionable") && (null !== $this->_versionable)) {
                $this->_versionable->insert();
            }
            if (property_exists($this, "_searchable") && (null !== $this->_searchable)) {
                $this->_searchable->insert(false);
            }

            // give related objects the chance to update their index
            $this->w->callHook("core_dbobject", "indexChange_".get_class($this), $this);
            
            // store this id in the context for hooks etc.
            $inserts = $this->w->ctx('db_inserts');
            if (!$inserts) {
                $inserts = array();
            }
            $inserts [get_class($this)] [] = $this->id;
            $this->w->ctx('db_inserts', $inserts);

            $this->commitTransaction();
        } catch (Exception $e) {
            // echo $e->getMessage();
            $this->w->Log->error("SQL ERROR: " . $e->getMessage());
            $this->w->Log->error("SQL: " . $this->_db->getSql());
            $this->rollbackTransaction();
            throw $e;
        }

        return true;
    }

    /**
     * Update an object
     *
     * if $force_null_values is true set null values in db, if false, null values in object will be ignored.
     *
     * @param boolean $force_null_values        	
     * @param boolean $force_validation        	
     * @return  boolean|array true or Array of validation errors
     */
    function update($force_null_values = false, $force_validation = true) {
        try {
            $this->startTransaction();

            if ($force_validation && property_exists($this, "_validation")) {
                $valid_response = $this->validate();
                if (!$valid_response ['success']) {
                    $this->rollbackTransaction();
                    return $valid_response;
                }
            }

            // calling hooks BEFORE updating the object
            $this->_callHooks("before", "update");

            $t = $this->getDbTableName();
            $columns = $this->getDbTableColumnNames();
            // check delete attribute
            if (in_array("is_deleted", $columns) && $this->is_deleted === null) {
                $this->is_deleted = 0;
            }

            // set default attributes the old way
            if (!property_exists($this, "_modifiable")) { // $this->_modifiable) {
                // if (property_exists($this, "dt_modified")) {
                if (in_array("dt_modified", $columns)) {
                    $this->dt_modified = time();
                }
                if (in_array("modifier_id", $columns) && $this->w->Auth->user()) {
                    $this->modifier_id = $this->w->Auth->user()->id;
                }
            }
            $data = array();
            foreach (get_object_vars($this) as $k => $v) {
                if ($k {0} != "_" && $k != "w") { // ignore volatile vars
                    $dbk = $this->getDbColumnName($k);

                    // call update conversions
                    $v = $this->updateConvert($k, $v);
                    if ($v !== null) {
                        $data [$dbk] = $v;
                    }
                    // if $force_null_values is TRUE and $v is NULL, then set fields in DB to NULL
                    // otherwise ignore NULL values
                    if ($v === null && $force_null_values == true) {
                        $data [$dbk] = null;
                    }
                }
            }

            $this->_db->update($t, $data)->where($this->getDbColumnName('id'), $this->id);
            $this->_db->execute();

            // calling hooks AFTER updating the object
            $this->_callHooks("after", "update");

            // call standard aspect methods
            if (property_exists($this, "_modifiable") && (null !== $this->_modifiable)) {
                $this->_modifiable->update();
            }
            if (property_exists($this, "_versionable") && (null !== $this->_versionable)) {
                $this->_versionable->update();
            }
            if (property_exists($this, "_searchable") && (null !== $this->_searchable)) {
                $this->_searchable->update(false);
            }
            
            // give related objects the chance to update their index
			$this->w->callHook("core_dbobject", "indexChange_".get_class($this), $this);
			
            // store this id in the context for hooks
            $updates = $this->w->ctx('db_updates');
            if (!$updates) {
                $updates = array();
            }
            $updates [get_class($this)] [] = $this->id;
            $this->w->ctx('db_updates', $updates);
            $this->commitTransaction();
        } catch (Exception $e) {
            // echo $e->getMessage();
            $this->w->Log->error("SQL ERROR: " . $e->getMessage());
            $this->w->Log->error("SQL: " . $this->_db->getSql());
            $this->rollbackTransaction();
            throw $e;
        }
        return true;
    }

    /**
     * create and execute a sql delete statement to delete this object from
     * the database.
     *
     * @param $force
     */
    function delete($force = false) {
        try {
            $this->startTransaction();

            // calling hooks BEFORE deleting the object
            $this->_callHooks("before", "delete");

            $t = $this->getDbTableName();
            $columns = $this->getDbTableColumnNames();

            // if an is_deleted property exists, then only set it to 1
            // and update instead of delete!
            if ((property_exists(get_class($this), "is_deleted") || (in_array("is_deleted", $columns))) && !$force) {
                $this->is_deleted = 1;
                // Hard code to NOT validate soft deletes
                $this->update(false, false);
            } else {
                $this->_db->delete($t)->where($this->getDbColumnName('id'), $this->id)->execute();
            }

            // calling hooks AFTER deleting the object
            $this->_callHooks("after", "delete");

            // give related objects the chance to update their index
            $this->w->callHook("core_dbobject", "indexChange_".get_class($this), $this);
            
            // store this id in the context for listeners
            $deletes = $this->w->ctx('db_deletes');
            if (!$deletes) {
                $deletes = array();
            }
            $deletes[get_class($this)][] = $this->id;
            $this->w->ctx('db_deletes', $deletes);

            // delete from search index
            if (property_exists($this, "_searchable") && (null !== $this->_searchable)) {
                $this->_searchable->delete();
            }
            $this->commitTransaction();
        } catch (Exception $e) {
            // echo $e->getMessage();
            $this->w->Log->error("SQL ERROR: " . $e->getMessage());
            $this->w->Log->error("SQL: " . $this->_db->getSql());
            $this->rollbackTransaction();
            throw $e;
        }
        return true;
    }

    /**
     * Returns the table name where this object is
     * stored
     * 
     * Uses either:
     * 
     * 1) the value of the property $_db_table (if it exists)
     * 2) the name of the class as "snake_case" (lowercase)
     * 
     * You can also override this function completely.
     *
     * @return String
     */
    function getDbTableName() {
        if (isset($this->_db_table)) {
            return $this->_db_table;
        } else if (isset(static::$_db_table)) {
            return static::$_db_table;
        } else {
            // Help from: http://www.tech-recipes.com/rx/5626/php-camel-case-to-spaces-or-underscore/
            return strtolower(preg_replace('/(?<=\\w)(?=[A-Z])/', "_$1", get_class($this)));
        }
        // return strtolower(get_class($this));
    }

    function getDbTableColumnNames() {
		if(!empty(self::$_columns[$this->_class])) {
			return self::$_columns[$this->_class];
		}
        $rs = $this->_db->_query('SELECT * FROM ' . $this->getDbTableName() . ' LIMIT 0');
        if ($rs !== false) {
            for ($i = 0; $i < $rs->columnCount(); $i++) {
                $col = $rs->getColumnMeta($i);
                self::$_columns[$this->_class][] = $col['name'];
            }
            return self::$_columns[$this->_class]; //$this->_db->prepare("DESCRIBE tablename")->execute()->fetchAll(PDO::FETCH_COLUMN);
        }
		self::$_columns[$this->_class][] = array();
        return array();
    }

    function getHumanReadableAttributeName($attribute) {
        // Remove magic markers (d_, dt_, etc)
        $replace_magic = array("d_", "dt_", "t_");
        foreach ($replace_magic as $rm) {
            if (substr($attribute, 0, strlen($rm)) == $rm) {
                $attribute = substr($attribute, strlen($rm));
                // There can be only one...
                break;
            }
        }

        // Remove underscores and " Id"
        $attribute = str_ireplace(array("_", " id"), array(" ", ""), $attribute);

        // Capitalise all
        $attribute = ucwords(trim($attribute));
        return $attribute;
    }

    /**
     * Returns the column name for a named attribute
     *
     * @param <type> $attr
     * @return <type>
     */
    function getDbColumnName($attr) {
        return $attr;
    }

    function _tn() {
        return $this->getDbTableName();
    }

    function _cn($attr) {
        return $this->getDbColumnName($attr);
    }

    /**
     * get Creator user object if creator_id
     * property exists
     * 
     * @return User
     */
    function getCreator() {
        if ($this->_modifiable) {
            return $this->_modifiable->getCreator();
        } else if (property_exists(get_class($this), "creator_id")) {
            return $this->w->Auth->getUser($this->creator_id);
        } else {
            return null;
        }
    }

    /**
     * get Modifier user object if creator_id
     * property exists
     * 
     * @return User
     */
    function getModifier() {
        if ($this->_modifiable) {
            return $this->_modifiable->getModifier();
        } else if (property_exists(get_class($this), "modifier_id")) {
            return $this->w->Auth->getUser($this->modifier_id);
        } else {
            return null;
        }
    }

    /**
     * Override this function if you want to add custom content
     * to the search index for this object. 
     * 
     * DO NOT CALL $this->getIndexContent() within this function
     * or you will create an endless loop which will destroy the universe!
     * 
     * @return String
     */
    function addToIndex() {
        
    }

    // a list of english words that need not be searched against
    // and thus do not need to be stored in an index
    static $_stopwords = "about above across after again against all almost alone along already also although always among and any anybody anyone anything anywhere are area areas around ask asked asking asks away back backed backing backs became because become becomes been before began behind being beings best better between big both but came can cannot case cases certain certainly clear clearly come could did differ different differently does done down downed downing downs during each early either end ended ending ends enough even evenly ever every everybody everyone everything everywhere face faces fact facts far felt few find finds first for four from full fully further furthered furthering furthers gave general generally get gets give given gives going good goods got great greater greatest group grouped grouping groups had has have having her here herself high higher highest him himself his how however important interest interested interesting interests into its itself just keep keeps kind knew know known knows large largely last later latest least less let lets like likely long longer longest made make making man many may member members men might more most mostly mrs much must myself necessary need needed needing needs never new newer newest next nobody non noone not nothing now nowhere number numbers off often old older oldest once one only open opened opening opens order ordered ordering orders other others our out over part parted parting parts per perhaps place places point pointed pointing points possible present presented presenting presents problem problems put puts quite rather really right room rooms said same saw say says second seconds see seem seemed seeming seems sees several shall she should show showed showing shows side sides since small smaller smallest some somebody someone something somewhere state states still such sure take taken than that the their them then there therefore these they thing things think thinks this those though thought thoughts three through thus today together too took toward turn turned turning turns two under until upon use used uses very want wanted wanting wants was way ways well wells went were what when where whether which while who whole whose why will with within without work worked working works would year years yet you young younger youngest your yours";

    /**
     * Consolidate all object fields into one big search friendly string.
     * 
     * @return string
     */
    function getIndexContent($ignoreAdditional = true) {

        // -------------- concatenate all object fields ---------------------		
        $str = "";
        $exclude = array("dt_created", "dt_modified", "id", "w");

        foreach (get_object_vars($this) as $k => $v) {
            if ($k{0} != "_" // ignore volatile vars
                    && (!property_exists($this, "_exclude_index") // ignore properties that should be excluded
                    || !in_array($k, $this->_exclude_index)) && stripos($k, "_id") === false && !in_array($k, $exclude)
            ) {
                $str .= $v . " ";
            }
        }

        // add custom content from the object to the index
        $str .= $this->addToIndex();

        // add content from hooks anywhere in the system
        if (!$ignoreAdditional) {
	        $additional = $this->w->callHook("core_dbobject", "add_to_index", $this);
        }
        
        if (!empty($additional)) {
			$str .= ' '.implode(" ",$additional); 
        }

        // ------------ sanitise string ----------------------------------
        // Remove all xml/html tags
        $str = strip_tags($str);

        // Remove case
        $str = strtolower($str);

        // Remove line breaks
        $str = str_replace("\n", " ", $str);

        // Remove all characters except A-Z, a-z, 0-9, dots, commas, hyphens, spaces and forward slashes (for dates) 
        // Note that the hyphen must go last not to be confused with a range (A-Z) 
        // and the dot, being special, is escaped with backslash
        $str = preg_replace("/[^A-Za-z0-9 \.,\-\/@':]/", '', $str);

        // Replace sequences of spaces with one space 
        $str = preg_replace('/  +/', ' ', $str);

        // de-duplicate string and remove any word shorter than 3 characters
        $temparr = array_filter(array_unique(explode(" ", $str)), function ($item) {
            return strlen($item) >= 3;
        });

        // remove stop words
        $temparr = array_diff($temparr, explode(" ", self::$_stopwords));
        $str = implode(" ", $temparr);

        return $str;
    }

    /**
     * Return an array of options for a field of this object.
     * This can then be used to create selects dropdown lists or radiobuttons.
     * The Html::form() and Html::multiColForm() functions will use this function
     * to create a select option list if no other options are given in the parameters
     * for this field.
     * 
     * There are 2 ways this function can be used ..
     * 
     * 1. You can just override it in your subclass and do what you want
     * 2. You can use the automagic properties in your subclass explained below
     * 
     * The return of this function should be an array that is fit for passing to Html::select(), eg.
     * 
     * a) array("Option1", "Option2", ..)
     * b) array(array("Title","Value"), array("Title","Value), ..)
     * c) array($dbobject1, $dbobject2, ..)
     * 
     * Automagic UI Field Hints
     * 
     * static $_<fieldname>_ui_select_string = array("option1","option2",...);
     * --> create a select dropdown using those strings explicitly
     *
     * static $_<fieldname>_ui_select_lookup_code = "states";
     * --> create a select dropdown and filling it with Lookup items from the database
     *     for the given code
     *
     * static $_<fieldname>_ui_select_objects_class = "Contact";
     * static $_<fieldname>_ui_select_objects_filter = array("is_deleted"=>0);
     * --> create a select filling it with the objects for the _class filtered by the _filter criteria
     * 
     * @param String $field
     * @return array
     */
    function getSelectOptions($field) {

        // check whether this field has hints
        $prop_string = "_" . $field . "_ui_select_strings";
        $prop_lookup = "_" . $field . "_ui_select_lookup_code";
        $prop_class = "_" . $field . "_ui_select_objects_class";
        $prop_filter = "_" . $field . "_ui_select_objects_filter";

        // prop string may be declared as static or (dynamic?) so we need to cater for both
        if (property_exists($this, $prop_string)) {
            $prop_detail = new ReflectionProperty($this, $prop_string);
            if ($prop_detail->isStatic()) {
                return $this::$$prop_string;
            } else {
                return $this->$prop_string;
            }
        } else if (property_exists($this, $prop_lookup) && $this->$prop_lookup) {
            return $this->getObjects("Lookup", array("type" => $this->$prop_lookup, "is_deleted" => 0));
        } else if (property_exists($this, $prop_class) && $this->$prop_class) {
            if (property_exists($this, $prop_filter) && $this->$prop_filter) {
                return $this->getObjects($this->$prop_class, $this->$prop_filter, true);
            } else {
                return $this->getObjects($this->$prop_class, null, true);
            }
        }
    }

    /**
     * Validate the object's properties according to the rules
     * defined in $_validation array.
     * 
     * @return void|multitype:multitype: boolean
     */
    function validate() {
        if (!property_exists($this, "_validation"))
            return;

        // Get table columns
        $table_columns = get_object_vars($this);
        $response = array(
            "valid" => array(),
            "invalid" => array(),
            "success" => false,
        );

        // Get validation rules that may be declared static
        $validation_rules = null;
        $prop_detail = new ReflectionProperty($this, "_validation");
        if ($prop_detail->isStatic()) {
            $validation_rules = $this::$_validation;
        } else {
            $validation_rules = $this->_validation;
        }

        // Loop through defined validation rules
        foreach ($validation_rules as $vr_key => $arr_rules) {
            // Check that what the user has provided is in our object
            if (!array_key_exists($vr_key, $table_columns)) {
                continue;
            }

            // Switch the rules... maybe there's a better way to do this :)
            foreach ($arr_rules as $rule => $rule_array) {
                if (is_string($rule_array)) {
                    $rule = $rule_array;
                }

                if (empty($this->$vr_key) && $rule !== "required") {
                    continue;
                }

                switch ($rule) {
                    case "required":
                        if (empty($this->$vr_key) && !is_numeric($this->$vr_key)) {
                            $response["invalid"]["$vr_key"][] = "Required Field";
                        } else {
                            $response["valid"][] = $vr_key;
                        }
                        break;
                    case "number":
                        // $this->$vr_key = filter_var($this->$vr_key, FILTER_SANITIZE_NUMBER_FLOAT);
                        if (!filter_var($this->$vr_key, FILTER_VALIDATE_FLOAT)) {
                            $response["invalid"]["$vr_key"][] = "Invalid Number";
                        } else {
                            $response["valid"][] = $vr_key;
                        }
                        break;
                    case "url":
                        // please be aware that this may accept invalid urls
                        // see http://www.php.net/manual/en/function.filter-var.php
                        $this->$vr_key = filter_var($this->$vr_key, FILTER_SANITIZE_URL);
                        if (!filter_var($this->$vr_key, FILTER_VALIDATE_URL)) {
                            $response["invalid"]["$vr_key"][] = "Invalid URL";
                        } else {
                            $response["valid"][] = $vr_key;
                        }
                        break;
                    case "email":
                        // please be aware that this may accept invalid emails
                        // see http://www.php.net/manual/en/function.filter-var.php
                        $this->$vr_key = filter_var($this->$vr_key, FILTER_SANITIZE_EMAIL);
                        if (!filter_var($this->$vr_key, FILTER_VALIDATE_EMAIL)) {
                            $response["invalid"]["$vr_key"][] = "Invalid Email";
                        } else {
                            $response["valid"][] = $vr_key;
                        }
                        break;
                    case "in":
                        // Case insensitive field check against an array of predefined values
                        if (is_array($rule_array)) {
                            $this->$vr_key = filter_var($this->$vr_key, FILTER_SANITIZE_STRING);
                            if (!in_array($this->$vr_key, $rule_array)) {
                                $response["invalid"]["$vr_key"][] = "Invalid value, allowed are " . implode(", ", $rule_array);
                            } else {
                                $response["valid"][] = $vr_key;
                            }
                        }
                        break;
                    case "unique":

                        break;
                    case "custom":
                    case "regex":
                        // Add surrounding regex slashes if they dont exist
                        if ($rule[0] !== '/')
                            $rule = '/' . $rule;
                        if ($rule[strlen($rule) - 1] !== '/')
                            $rule = $rule . '/';

                        if (!filter_var($this->$vr_key, FILTER_VALIDATE_REGEXP, array('regexp' => $rule))) {
                            $response["invalid"]["$vr_key"][] = "Invalid";
                        } else {
                            $response["valid"][] = $vr_key;
                        }
                        break;
                }
            }
        }

        // else do nothing and let execution proceed as usual
        // Set response value to if the validation was succesful

        if (count($response["invalid"]) == 0) {
            $response["success"] = true;
        }

        return $response;

        // die(); // debugging only
        // if validation fails return to invoked page with errors... how to transport them though?
        // this function is called deep in code so the initiator of the update or insert function may not know what's
        // going on ... redirecting away to another page from here is ... rude.
        // better to do the following:
        // - update or insert just fail but send an exception containing the invalid messages
        // - caller should call validate BEFORE update / insert to react to messages in a UI fashion (eg. redisplay form with message, etc)
// 		if (count($response["invalid"]) > 0){
// 			$_SESSION["errors"] = $response["invalid"]; // <-- GENIUS!... hopefully that works
// 			$this->w->redirect($this->w->localUrl($_SERVER["REDIRECT_URL"]));
// 		}
    }

    /**
     * Convert data values before sending to database
     *
     * @param string $k
     * @param mixed $v
     * @return mixed
     */
    function updateConvert($k, $v) {
        if (strpos($k, "dt_") === 0) {
            if (!empty($v)) {
                return $this->time2Dt($v);
            } else
                return null;
        } else if (strpos($k, "d_") === 0) {
            if (!empty($v)) {
                return $this->time2D($v);
            } else
                return null;
        } else if (strpos($k, "t_") === 0) {
            if (!empty($v) && is_int($v)) {
                return $this->time2T($v);
            } else
                return null;
        } else if (strpos($k, "s_") === 0) {
            if (!empty($v)) {
                return AESencrypt($v, Config::get('system.password_salt'));
            }
        }
        return $v;
    }

	public function __toString() {
		return $this->printSearchTitle();
	}
	
}
