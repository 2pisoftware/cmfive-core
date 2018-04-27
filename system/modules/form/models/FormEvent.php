<?php
/**
 * This class represents a form event definition that is used in a form
 */
class FormEvent extends DbObject {

	public $form_id;
	public $title;
	public $description;
	public $type;
	public static $_type_ui_select_options = ['On Created','On Modified','On Deleted'];
	public $is_active;

	//get processors for event
	public function getEventProcessors() {
		return $this->getObjects('FormEventProcessor',['form_event_id'=>$this->id,'is_deleted'=>0]);
	} 
}