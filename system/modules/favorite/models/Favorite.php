<?php
/**
 *  Favorite class for flagging records
 * 
 * @author Steve Ryan, steve@2pisoftware.com, 2015
 */
class Favorite extends DbObject {
	
	// object properties
	
	// public $id; <-- this is defined in the parent class
	public $object_class;
	public $object_id;
	public $user_id;
	
	// standard system properties
	
	public $is_deleted; // <-- is_ = tinyint 0/1 for false/true
	public $dt_created; // <-- dt_ = datetime values
	public $dt_modified;
	public $modifier_id; // <-- foreign key to user table
	public $creator_id; // <-- foreign key to user table
	
	function getLinkedObject() {
		return $this->getObject($this->object_class, $this->object_id);
	}
	
	// functions for implementing access restrictions, these are optional

	public function canList(User $user) {
		return $user !== null && $user->hasAnyRole(array("favorites_user"));
	}
	
	public function canView(User $user) {
		return $user !== null && $user->hasAnyRole(array("favorites_user"));
	}
	
	public function canEdit(User $user) {
		return $user !== null && $user->hasAnyRole(array("favorites_user"));
	}
	
	public function canDelete(User $user) {
		return $user !== null && $user->hasAnyRole(array("favorites_user"));
	}	
	
		
}
