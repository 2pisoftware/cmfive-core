<?php

class FormApplicationMember extends DbObject {
	
	public static $_roles = ['OWNER', 'EDITOR', 'VIEWER'];

	public $application_id;
	public $member_user_id;
	
	/*
	 * OWNER - can invite and remove other members
	 * EDITOR - can view, edit and delete form entries
	 * VIEWER - can view form entries
	 */
	public $role;

	public function getName() {
		$user = $this->getObject('User', $this->member_user_id);
		if (!empty($user)) {
			$contact = $user->getContact();
			if (!empty($contact)) {
				return $contact->getFullName();
			}
		}

		return '';
	}
}