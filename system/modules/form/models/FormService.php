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
	 * @param  Mixed $id
	 * @return Form|null
	 */
	public function getForm($id) {
		return $this->getObject("Form", $id);
	}
	
	/**
	 * Load a form field by id
	 * 
	 * @param  Mixed $id
	 * @return FormField|null
	 */
	public function getFormField($id) {
		return $this->getObject("FormField", $id);
	}
	
	/**
	 * Load a form instance by id
	 *
	 * @param  Mixed $id
	 * @return FormInstance|null
	 */
	public function getFormInstance($id) {
		return $this->getObject("FormInstance", $id);
	}

	/**
	 * Returns a form value by id
	 * 
	 * @param  Mixed $id
	 * @return FormValue|null 
	 */
	public function getFormValue($id) {
		return $this->getObject('FormValue', $id);
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

		$application_mapping = [];
		if ($object instanceof FormApplication) {
			$application_mapping = $this->getObjects('FormApplicationMapping', ['application_id' => $object->id, 'is_deleted' => 0]);
		}

		return count($mapping) > 0 || count($application_mapping) > 0;
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
		
		$application_forms = [];
		if ($object instanceof FormApplication) {
			$application_forms = $this->getObjects('FormApplicationMapping', ['application_id' => $object->id, 'is_deleted' => 0]);
			if (!empty($application_forms)) {
				foreach($application_forms as $application_form) {
					$forms[] = $application_form->getForm();
				}
			}
		}

		return $forms;
	}
	
	/**
	 * Load the form instances for a given form and object
	 *
	 * @return FormInstance[]
	 */
	public function getFormInstancesForFormAndObject($form, $object, $page = null, $pagesize = null) {
		return $this->getObjects("FormInstance", ["form_id" => $form->id, "object_class" => get_class($object), "object_id" => $object->id, "is_deleted" => 0], false, true, null, $page, $pagesize);
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
	
	/**
	 * Load an Application
	 *
	 * @param unknown $application_id
	 */
	public function getFormApplication($application_id) {
		return $this->getObject("FormApplication",["id" => $application_id, "is_deleted" => 0]);
	}

	public function getFormValueForInstanceAndField($instance_id, $field_id) {
		return $this->getObject('FormValue', ['form_instance_id' => $instance_id, 'form_field_id' => $field_id, 'is_deleted' => 0]);
	}

	/**
	 * Submenu navigation for Forms
	 * 
	 * @param  Web    $w
	 * @param  String $title
	 * @param  Array $prenav
	 * @return Array
	 */
	public function navigation(Web $w, $title = null, $prenav = null) {
        if ($title) {
            $w->ctx("title", $title);
        }
		
        $nav = $prenav ? $prenav : array();
        if ($w->Auth->loggedIn()) {
            $w->menuLink("form-application", "Applications", $nav);
            $w->menuLink("form", "Forms", $nav);
        }

        return $nav;
    }
}
