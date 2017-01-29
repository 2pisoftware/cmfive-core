<?php
/**
 * This class provides various lookup functions for Form object
 */
class FormService extends DbService {
	
	/**
	 * Load all Forms
	 * 
	 * @return Forms[]
	 */
	public function getForms() {
		return $this->getObjects("Form", ["is_deleted" => 0]);
	}
	
	/**
	 * Load a form by id
	 * 
	 * @return Form
	 */
	public function getForm($id) {
		return $this->getObject("Form", $id);
	}
	
	/**
	 * Load a form field by id
	 * 
	 * @return FormField
	 */
	public function getFormField($id) {
		return $this->getObject("FormField", $id);
	}
	
	/**
	 * Load a form instance by id
	 * 
	 * @return FormInstance
	 */
	public function getFormInstance($id) {
		return $this->getObject("FormInstance", $id);
	}
	
	/**
	 * Get an array structure describing the form for use with 
	 * Html:multiColForm
	 * 
	 * @return  array[]
	 */
	public function buildForm(FormInstance $form_instance, Form $form) {
		$form_structure = $form_instance->getEditForm($form);
		return $form_structure;
	}
	
	/**
	 * Check if this form is mapped to the object.
	 * 
	 * @return boolean
	 */
	public function isFormMappedToObject($form, $object) {
		$mapping = $this->getObject("FormMapping", ["form_id" => $form->id, "object" => $object, "is_deleted" => 0]);
		return !empty($mapping->id);
	}

	/**
	 * Check if any forms are mapped to this object
	 * 
	 * @return boolean
	 */
	public function areFormsMappedToObject($object) {
		$mapping = $this->getObjects("FormMapping", ["object" => get_class($object), "is_deleted" => 0]);
		return count($mapping) > 0;
	}
	
	/**
	 * Load the forms that are mapped to this object
	 * 
	 * @return Form[]
	 */
	public function getFormsMappedToObject($object) {
		$mapping = $this->getObjects("FormMapping", ["object" => get_class($object), "is_deleted" => 0]);
		$forms = [];
		if (!empty($mapping)) {
			foreach($mapping as $map) {
				$forms[] = $map->getForm();
			}
		}
		
		return $forms;
	}
	
	/**
	 * Load the form instances for a given form and object
	 *
	 * @return FormInstance[]
	 */
	public function getFormInstancesForFormAndObject($form, $object) {
		return $this->getObjects("FormInstance", ["form_id" => $form->id, "object_class" => get_class($object), "object_id" => $object->id, "is_deleted" => 0]);
	}
	
	/**
	 * Counts the number of form instances attached to an object
	 * 
	 * @param Form $form
	 * @param DbObject $object
	 * return int
	 */
	public function countFormInstancesForFormAndObject($form, $object) {
		return $this->w->db->get('form_instance')->where("form_id", $form->id)
					->where("object_class", get_class($object))->where("object_id", $object->id)
					->where("is_deleted", 0)->count();
	}
	
	/**
	 * Load a form field by form and field name
	 * 
	 * @return FormField
	 */
	public function getFormFieldByFormIdAndTitle($form_id, $name) {
		return $this->getObject("FormField", ["form_id" => $form_id, "technical_name" => $name, "is_deleted" => 0]);
	}
}
