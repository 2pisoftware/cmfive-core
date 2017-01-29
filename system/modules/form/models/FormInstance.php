<?php
/**
 * This class represents a single record of information stored in a form.
 */
class FormInstance extends DbObject {
	
	public $form_id;
	public $object_class;
	public $object_id;
	
	/**
	 * Load the object that this form instance is related to eg Task
	 * 
	 * @return DbObject|null
	 */
	public function getLinkedObject() {
		return $this->getObject($this->object_class, $this->object_id);
	}
	
	/**
	 * Load the form that this form instance is related to 
	 * 
	 * @return Form|null
	 */
	public function getForm() {
		return $this->getObject("Form", $this->form_id);
	}
	
	/**
	 * Load the values that have been entered into all form fields
	 * for this instance.
	 * 
	 * @return FormValue[]
	 */
	public function getSavedValues() {
		return $this->getObjects("FormValue", ["form_instance_id" => $this->id, "is_deleted" => 0]);
	}
	
	/**
	 * Generate the contents of a table row as HTML
	 * Use a template associated with the form if available.
	 * Template is parsed using system based twig parser.
	 * The form values are available as template data keyed against their
	 * technical names. eg <td>{{title}}</td>
	 * 
	 * @return string 
	 */
	public function getTableRow() {
		$form_values = $this->getSavedValues();
		
		$form = $this->getForm();
		
		// If there is a row template specified then use that to display
		// The downside is that (for now) the template will need to implement its own
		// masking on values
		if (!empty($form->row_template)) {
			// Flatten the values array
			$template_data = [];
			if (!empty($form_values)) {
				foreach($form_values as $form_value) {
					$field = $form_value->getFormField();
					$template_data[$field->technical_name] = $form_value->value;
				}
			}
			
			return $this->w->Template->render($form->row_template, $template_data);
		}
		// NO TEMPLATE
		$table_row = '';
		$formValueCollated=[];
		// collate available form values
		if (!empty($form_values)) {
			foreach($form_values as $value) {
				$formValueCollated[$value->form_field_id]=$value;
			}
		}
		
		$form_fields = $form->getFields();
		if (!empty($form_fields)) {
			foreach($form_fields as $field) {
				if (!empty($formValueCollated[$field->id])) {
					$table_row .= "<td>" . $formValueCollated[$field->id]->getMaskedValue() . "</td>";
				} else {
					$table_row .= "<td>&nbsp;</td>";
				}
			}
		}
		
		return $table_row;
	}
	
	/**
	 * Return an array representing the complete structure of a form to 
	 * use with Html::multiColForm()
	 * @return  array[]
	 */
	public function getEditForm($form) {
		if (empty($form->id)) {
			$form = $this->getForm();
			if (empty($form->id)) {
				$form = new Form($this->w);
			}
		}
		
		$form_values = $this->getSavedValues();
		$form_structure = []; // $w->Form->buildForm($this);
		$formValueCollated=[];
		// collate available form values
		if (!empty($form_values)) {
			foreach($form_values as $value) {
				$formValueCollated[$value->form_field_id]=$value;
			}
		}
		
		$form_fields = $form->getFields();
		if (!empty($form_fields)) {
			foreach($form_fields as $field) {
				if (!empty($formValueCollated[$field->id])) {
					$form_structure[] = array($formValueCollated[$field->id]->getFormRow());
				} else {
					$form_structure[] = array($field->getFormRow());
				}
			}
		}
		return array($form->title => $form_structure);
	}
	
	/**
	 * Can the user list this form instance
	 * The following can* functions a overridden to implement the linked
	 * objects own matching functions.
	 * 
	 * The use case is, for example, if a user can view a Task but not edit
	 * then those permissions are reflect in the attached form data
	 * 
	 * @return boolean
	 */
	public function canList(\User $user) {
		$object = $this->getLinkedObject();
		if (!empty($object->id)) {
			return $object->canList($user);
		}
		
		return parent::canList($user);
	}
	
	/**
	 * Can the user view this form instance
	 * 
	 * @return boolean
	 */	
	 public function canView(\User $user) {
		$object = $this->getLinkedObject();
		if (!empty($object->id)) {
			return $object->canView($user);
		}
		
		return parent::canView($user);
	}
	
	/**
	 * Can the user edit this form instance
	 * 
	 * @return boolean
	 */	
	 public function canEdit(\User $user) {
		$object = $this->getLinkedObject();
		if (!empty($object->id)) {
			return $object->canEdit($user);
		}
		
		return parent::canEdit($user);
	}
	
	/**
	 * Can the user delete this form instance
	 * 
	 * @return boolean
	 */	
	 public function canDelete(\User $user) {
		$object = $this->getLinkedObject();
		if (!empty($object->id)) {
			return $object->canDelete($user);
		}
		
		return parent::canDelete($user);
	}
}
