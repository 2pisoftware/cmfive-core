<?php
class Inbox_message extends DbObject {
	public $digest;
	public $message;
	
	public $__use_auditing = false;

	function insert($force_validation = false) {
		$this->digest = sha1($this->message);
		parent::insert();
	}
}