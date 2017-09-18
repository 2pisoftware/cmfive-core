<?php
/**
 * This class represents a form that can be associated with other objects
 * in the system.
 *
 * @author Adam Buckley <adam@2pisoftware.com>
 */
class Form extends DbObject {
	
	public $title;
	public $description;
	public $header_template;   // if specified this string is used as the form table header
	public $row_template;  	// if specified this string is used as a template for rendering a form row
	public $summary_template;  // if specified this string is used as a template for rendering a form summary row
	
	/**
	 * Load the fields associated with this form
	 * 
	 * @return Array<FormField>
	 */
	public function getFields() {
		return $this->getObjects("FormField", ["form_id" => $this->id, "is_deleted" => 0], false, true, "ordering ASC");
	}
	
	/**
	 * Generate the header row for the form table
	 * 
	 * @return string
	 */
	public function getTableHeaders() {
		if (!empty($this->header_template)) {
			return $this->header_template;
		}
		
		$fields = $this->getFields();
		
		$header_string = '';
		if (!empty($fields)) {
			foreach($fields as $field) {
				$header_string .= '<th>' . $field->name . '</th>';
			}
		}
		
		return $header_string;
	}
	
	/**
	 * Generate the summary row for the form table
	 *
	 * @param DbObject $object Linked object
	 * @return string
	 */
	public function getSummaryRow($object) {
		if (!empty($this->summary_template)) {
			$instances = $this->getFormInstancesForObject($object);
			
			// Generate a more accessible structure of the form instances and its data
			$structure = [];
			if (!empty($instances)) {
				foreach($instances as $instance) {
					$saved_values = $instance->getSavedValues();
					
					if(!empty($saved_values)) {
						$instance_structure = [];
						foreach($saved_values as $saved_value) {
							$field = $saved_value->getFormField();
							$instance_structure[$field->technical_name] = $saved_value->value;
						}
					}
					
					$structure[] = $instance_structure;
				}
			}

			return $this->w->Template->render($this->summary_template, ["form" => $structure]);
		}
		return '';
	}
	
	/**
	 * Load the form instances containing submitted data for this form
	 * 
	 * @return Array<FormInstance>
	 */
	public function getFormInstances() {
		return $this->getObjects("FormInstance", ["form_id" => $this->id, "is_deleted" => 0]);
	}
	
	/**
	 * Load the form instances containing submitted data for this form
	 * that are related to the $object parameter
	 *
	 * @param  DbObject $object Linked object
	 * @return Array<FormInstance>
	 */
	public function getFormInstancesForObject($object) {
		return $this->w->Form->getFormInstancesForFormAndObject($this, $object);
	}
	
	/**
	 * Returns number of instances of submitted data attached to this form
	 * 
	 * @param  DbObject $object Linked object
	 * @return int Number of objects
	 */
	public function countFormInstancesForObject($object) {
		return $this->w->Form->countFormInstancesForFormAndObject($this, $object);
	}
	
	/**
	 * Generate label to show this record in select inputs
	 * 
	 * @return string
	 */
	public function getSelectOptionTitle() {
		return $this->title;
	}
	
	/**
	 * Generate value to use for this record in select inputs
	 * 
	 * @return string
	 */
	public function getSelectOptionValue() {
		return $this->id;
	}
	
	/**
	 * Generate text to show this record in search results  
	 * 
	 * @return string
	 */
	public function printSearchTitle() {
		return $this->title;
	}
	
	/**
	 * Generate a link to show this form
	 * 
	 * @return string
	 */
	public function printSearchUrl() {
		return "/form/show/" . $this->id;
	}

}
