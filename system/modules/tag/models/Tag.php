<?php
class Tag extends DbObject {
	public $id;
	public $obj_class;   // varchar      
	public $obj_id;
	public $tag_color;
	public $tag;

	public $creator_id;
	public $dt_created;
	public $modifier_id;
	public $dt_modified;
	public $is_deleted;

	public static $_db_table = "tag";
	
	function getSelectOptionTitle() {
		return $this->tag;
	}
	function getSelectOptionValue() {
		return $this->tag;
	}
	
	public function insert($force_validation = true) {
		parent::insert($force_validation);
		// Call Hook
		$this->w->callHook("tag", "tag_added_" . $this->obj_table, $this);
	}
}