<?php
/**
 * Implementing the Versionable Aspect which uses
 * ObjectHistory entries for storing versions
 * of an object
 */
class AspectVersionable {
	private $object;
	private $create_version = true;

	function __construct(DbObject &$obj) {
		$this->object = $obj;
	}

	/////////////////////////////////////////////////
	// Methods to be used by DbObject
	/////////////////////////////////////////////////

	/**
	 * this update function will create a new historic object
	 * for these values before updating the current record
	 */
	function update() {
		// save all values in a new historic object before updating
		if ($this->create_version) {
			$ho = new ObjectHistory($this->object->w);
			$ho->fillFromObject($this->object);
			$ho->insert();
		}
	}

	/**
	 * this update function will create a new historic object
	 * for these values before updating the current record
	 */
	function insert() {
		$ho = new ObjectHistory($this->object->w);
		$ho->fillFromObject($this->object);
		$ho->insert();
	}

	/////////////////////////////////////////////////
	// Methods to be used from client objects
	/////////////////////////////////////////////////


	/**
	 * get all history data for this object
	 */
	function getAllVersions() {
		return null;
	}

	/**
	 * get a specific version by id
	 * @param $id
	 */
	function getVersion($id) {
		return null;
	}

	/**
	 * get the very latest version
	 */
	function getLastVersion() {
		return null;
	}
}

//////////////////////////////////////////////////////////////////////////////
//            Automatic History for some objects
//////////////////////////////////////////////////////////////////////////////

/**
 * Stores data about updates to any object
 */
class ObjectHistory extends DbObject {
	public $class_name;
	public $object_id;

	// has ModifiableAspect!!
	public $__is_modifiable;

	// do not audit this table!
	public $__use_auditing = false;

	function getDbTableName() {
		return "object_history";
	}

	function getCurrentObject() {
		return $this->getObject($this->class_name, $this->object_id);
	}

	function getValues() {
		return $this->getObjects("ObjectHistoryEntry", array("history_id",$this->id));
	}

	function fillFromObject($obj) {
		 
	}
}

/**
 * stores single field values for a historic object
 */
class ObjectHistoryEntry extends DbObject {
	public $history_id;
	public $attr_name;
	public $attr_value;

	// do not audit this table!
	public $__use_auditing = false;

	function getDbTableName() {
		return "object_history_entry";
	}
}

