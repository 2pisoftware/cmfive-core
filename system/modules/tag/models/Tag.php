<?php

class Tag extends DbObject {

	public $tag;

	/**
	 * Returns all tag assign objects
	 * 
	 * @return Array<TagAssign>
	 */
	public function getAssignedObjects() {
		return $this->getObjects('TagAssign', ['tag_id' => $this->id]);
	}
	
	
	/**
	 * Counts all assigned objects
	 * 
	 * @return int
	 */
	public function countAssignedObjects() {
		return $this->_db->get('tag_assign')->where('tag_id', $this->id)->and('is_deleted', 0)->count();
	}
	
	/**
	 * Display tag override for select
	 * 
	 * @return String
	 */
	public function getSelectOptionTitle() {
		return $this->tag;
	}
	
	/**
	 * Delete override to remove object tag associations
	 * 
	 * @param boolean $force
	 */
	public function delete($force = false) {
		$assigned_objects = $this->getAssignedObjects();
		if (!empty($assigned_objects)) {
			foreach($assigned_objects as $assigned_object) {
				$assigned_object->delete($force);
			}
		}
		
		parent::delete($force);
	}
	
}