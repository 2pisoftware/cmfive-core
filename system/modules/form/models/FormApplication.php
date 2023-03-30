<?php

/**
 * A form application is a group of members and various forms
 * It allows for either ad-hoc storage of structural data
 * or quick prototyping of new modules
 * 
 * @author Carsten Eckelmann <carsten@2pisoftware.com>
 * @author Adam Buckley <adam@2pisoftware.com>
 */
class FormApplication extends DbObject {
	
	public $title;
	public $description;
	public $is_active;
	public $is_deleted;
	
	public function getForms() {
		$forms_mapped = $this->w->db->get('form')->leftJoin('form_application_mapping on form_application_mapping.form_id = form.id')
									->where('form.is_deleted', 0)->where('form_application_mapping.is_deleted', 0)
									->where('form_application_mapping.application_id', $this->id)->fetchAll();

		return $this->getObjectsFromRows('Form', $forms_mapped);
		// return FormService::getInstance($this->w)->getFormsMappedToObject($this);
	}

	public function hasForm($form) {
		return $this->getObject("FormApplicationMapping", ['application_id'=>$this->id,'form_id'=>$form->id,'is_deleted'=>0]) ? 1 : 0;
	}
	
	public function getFormInstances($form) {
		return FormService::getInstance($this->w)->getFormInstancesForFormAndObject($form, $this);
	}
	
	public function getMembers() {
		return $this->getObjects("FormApplicationMember", ['application_id' => $this->id, 'is_deleted' => 0]);
	}

	public function getMapping() {
		return $this->getObjects("FormApplicationMapping", ["application_id" => $this->id, "is_deleted" => 0]);
	}
	
	private function _getApplicationMember($user) {
		if ($user == null) return null;
		return $this->getObject("FormApplicationMember", ['application_id' => $this->id, 'member_user_id' => $user->id, 'is_deleted' => 0]);
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