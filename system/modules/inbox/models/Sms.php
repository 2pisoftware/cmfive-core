<?php
class Sms extends DbObject {
	public $phone;
	public $message;
	public $dt_created;
	public $creator_id;
	
	function getDbTableName() {
		return "sms";
	}
	
	function send() {
		sendSMS(array($this->phone),$this->message,$this->w->Auth->user()->login);
		
		// always store a fresh line item
		$this->dt_created = null;
		$this->creator_id = null;
		$this->id = null;
		$this->insert();
	}
}