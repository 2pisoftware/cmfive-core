<?php
/**
 * Use this aspect to store creation
 * and modification data for any object
 * @author carsten
 *
 */
class AspectModifiable {
	private $object;
	private $_mo;

	function __construct(DbObject &$obj) {
		$this->object = $obj;
	}

	private function getMo() {
		if ($this->object && $this->object->id && !$this->_mo) {
			$this->_mo = $this->object->getObject("ObjectModification", array("table_name"=>$this->object->getDbTableName(), "object_id"=>$this->object->id));
		}
		return $this->_mo;
	}

	/////////////////////////////////////////////////
	// Methods to be used by DbObject
	/////////////////////////////////////////////////

	/**
	 * Store creation data
	 */
	function insert() {
		if (!$this->getMo()) {
			$mo = new ObjectModification($this->object->w);
			$mo->table_name = $this->object->getDbTableName();
			$mo->object_id = $this->object->id;
			$mo->dt_created = time();
			$user = $mo->w->Auth->user();
			$mo->creator_id = (!empty($user->id) ? $user->id : 0);
			$mo->insert();
		}
	}

	/**
	 * Store modification data
	 */
	function update() {
		if ($this->getMo()) {
			$this->_mo->dt_modified = time();
			$user = $this->_mo->w->Auth->user();
			$this->_mo->modifier_id = (!empty($user->id) ? $user->id : 0);
			$this->_mo->update();
		}
	}

	/////////////////////////////////////////////////
	// Methods to be used by client object
	/////////////////////////////////////////////////

	function getCreator() {
		if ($this->getMo()) {
			return $this->_mo->getCreator();
		}
	}

	function getModifier() {
		if ($this->getMo()) {
			return $this->_mo->getModifier();
		}
	}

	function getCreatedDate() {
		if ($this->getMo()) {
			return $this->_mo->dt_created;
		}
	}

	function getModifiedDate() {
		if ($this->getMo()) {
			return $this->_mo->dt_modified;
		}
	}


}

//////////////////////////////////////////////////////////////////////////////
//            Generic Modification data for some objects
//////////////////////////////////////////////////////////////////////////////

/**
 * Store creation and modification data of any object
 */
class ObjectModification extends DbObject {
	public $table_name;
	public $object_id;

	public $dt_created;
	public $dt_modified;
	public $creator_id;
	public $modifier_id;

	// do not audit this table!
	public $__use_auditing = false;

	public static $_db_table = "object_modification";
	
	/**
	 * returns the creator of the
	 * object which is attached to this
	 * aspect.
	 *
	 * @return User
	 */
	function getCreator() {
		if ($this->creator_id) {
			return $this->w->Auth->getUser($this->creator_id);
		}
	}

	/**
	 * returns the modifier user
	 * of the object which is attached
	 * to this aspect.
	 *
	 * @return User
	 */
	function getModifier() {
		if ($this->modifier_id) {
			return $this->w->Auth->getUser($this->modifier_id);
		}
	}

}

