<?php
/**
 * This class represents a form event processor definition that is used in a form
 */
class FormEventProcessor extends DbObject {
	public $form_event_id;
	public $class;
	public $module;
	public $name;
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

    public function getEvent() {
    	return $this->getObject('FormEvent',$this->form_event_id);
    }
}