<?php
/**
 * Use this aspect to create automatic full text indexing
 * for any object marked with this aspect.
 * 
 * Switch this on by creating a property called
 * 
 *    public $_searchable;
 * 
 * To exclude certain properties from being indexed:
 * 
 *    public $_exclude_index = array("prop1","prop2");
 * 
 * The old style modification properties will be automatically ignored:
 * 
 *    dt_created, dt_modified, creator_id, modifier_id
 * 
 * Also any property that ends with _id will be ignored.
 * 
 * Volatile properties that start with $_ will be ignored
 * 
 * It is possible to add custom content to the indexed string. Just create a method:
 * 
 * function addToIndex() {}
 * 
 * Which should return the string that needs to be added to the indexed content. 
 * All sanitising and de-duplication will be performed on that string as well.
 * 
 * @author carsten@tripleacs.com 2013
 *
 */
class AspectSearchable {
	private $object;
	private $_index;
		
	function __construct(DbObject &$obj) {
		$this->object = $obj;
	}
	
	private function getIndex() {
		if ($this->object && $this->object->id && !$this->_index) {
			$this->_index = $this->object->getObject("ObjectIndex", 
					array("class_name"=>get_class($this->object), 
							"object_id"=>$this->object->id));
		}
		return $this->_index;
	}
	
	/**
	 * Create index entry for new objects
	 */
	function insert($ignoreAdditional = true) {
		if ($this->object->shouldAddToSearch()) {
			//adding to search
			$index = $this->getIndex();
			if (empty($index)) {
				$io = new ObjectIndex($this->object->w);
				$io->class_name = get_class($this->object);
				$io->object_id = $this->object->id;
				$io->dt_created = time();
				$io->creator_id = (AuthService::getInstance($this->object->w)->loggedIn() ? AuthService::getInstance($this->object->w)->user()->id : 0);
				$io->content = $this->object->getIndexContent($ignoreAdditional);
				$io->insert();
			}
		} else {
			//removing from search
			$index = $this->getIndex();
			if (!empty($index)) {
				$this->delete();
			}
		}
	}
	
	/**
	 * Update index for updated object
	 */
	function update($ignoreAdditional = true) {
		if ($this->object->shouldAddToSearch()) {
			$index = $this->getIndex();
			if (empty($index)) {
				$this->insert($ignoreAdditional);
			} else {
				$this->_index->dt_modified = time();
				$this->_index->modifier_id = (AuthService::getInstance($this->_index->w)->loggedIn() ? AuthService::getInstance($this->_index->w)->user()->id : 0);
				
				$this->_index->content = $this->object->getIndexContent($ignoreAdditional);
						
				$this->_index->update();
			}
		} else {
			$index = $this->getIndex();
			if (!empty($index)) {
				$this->delete();
			}
		}
	}
	
	/**
	 * Delete index entry for deleted objects
	 * 
	 * The object may only be marked as deleted, but nevertheless it should not be used any more
	 * for searches!
	 */
	function delete() {
		if ($this->getIndex()) {
			$this->_index->delete();
		}
	}
	
	
}

class ObjectIndex extends DbObject {
	public $class_name;
	public $object_id;
	public $content;
	
	public $dt_created;
	public $dt_modified;
	public $creator_id;
	public $modifier_id;
	
	// do not audit this table!
	public $__use_auditing = false;
	
	function getDbTableName() {
		return "object_index";
	}	
}
