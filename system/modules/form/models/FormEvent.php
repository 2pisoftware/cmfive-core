<?php
/**
 * This class represents a form event definition that is used in a form
 */
class FormEvent extends DbObject {

	public $form_id;
	public $form_application_id;
	public $title;
	public $event_type;
	public static $_event_type_ui_select_options = ['On Created','On Modified','On Deleted'];
	public $is_active;
	public $class;
	public $module;
	public $processor_settings;
	public $settings;


	public function retrieveProcessor() {
        try {
            $processor = new $this->class($this->w);
            return $processor;
        } catch (Exception $e) {
            return null;
        }
    }
}