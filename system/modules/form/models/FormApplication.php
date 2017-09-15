<?php

/**
 * A form application is a group of members and various forms
 * It allows for either ad-hoc storage of structural data
 * or quick prototyping of new modules
 * 
 * @author careck
 *
 */
class FormApplication extends DbObject {
	
	public $title;
	public $description;
	public $is_active;
	public $is_deleted;
	
	public function getForms() {
		return $w->Form->getFormsMappedToObject($this);
	}
	
	public function getFormInstances($form) {
		return $w->Form->getFormInstancesForFormAndObject($form, $this);
	}
	
	public function getMembers() {
		return $this->getObjects("FormApplicationMember",['application_id'=>$this->id]);
	}
	
	private function _getApplicationMember($user) {
		if ($user == null) return null;
		return $this->getObject("FormApplicationMember", ['application_id'=>$this->id,'member_user_id'=>$user->id]);
	}
	
	public function isMember($user) {
		return $this->_getApplicationMember($user) !== null;
	}
	
	public function isOwner($user) {
		return $this->isMember($user) && $this->_getApplicationMember($user)->role == "OWNER";
	}

	public function isEditor($user) {
		return $this->isMember($user) && $this->_getApplicationMember($user)->role == "EDITOR";
	}

	public function isViewer($user) {
		return $this->isMember($user) && $this->_getApplicationMember($user)->role == "VIEWER";
	}
	
}