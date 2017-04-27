<?php

class FormApplicationMember extends DbObject {
	
	public $application_id;
	public $member_user_id;
	
	/*
	 * OWNER - can invite and remove other members
	 * EDITOR - can view, edit and delete form entries
	 * VIEWER - can view form entries
	 */
	public $role; 
}