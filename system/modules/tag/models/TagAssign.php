<?php

class TagAssign extends DbObject {
	
	public $tag_id;
	public $object_class;
	public $object_id;
	
	public function getAssignedObject() {
		if (class_exists($this->object_class)) {
			return $this->getObject($this->object_class, $this->object_id);
		}
	}
	
	public function getTag() {
		return $this->getObject('Tag', $this->tag_id);
	}
	
}