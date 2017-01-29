<?php
class Lookup extends DbObject {
	public $weight;
	public $type;
	public $code;
	public $title;
	public $is_deleted;

	function getDbTableName() {
		return "lookup";
	}
	
	function getSelectOptionValue() {
		return $this->code;
	}
	
	function getSelectOptionTitle() {
		return $this->title;
	}
}