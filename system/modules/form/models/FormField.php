<?php
/**
 * This class represents a form field definition that is used in a form
 */
class FormField extends DbObject {
	
	public $form_id;
	public $name;
	public $technical_name;
	public $interface_class;
	public $type;
	public $mask;
	public $ordering;

	/**
	 * Load the form definition associated with this form field
	 * @return Form
	 */
	public function getForm() {
		return $this->getObject("Form", $this->form_id);
	}
	
	/**
	 * Override insert to allow munging of technical name and setting
	 * the FormField interface class eg FormStandardInterface 
	 * @return  boolean|array true or Array of validation errors
	 */
	public function insert($force_validation = true) {
		$this->technical_name = strtolower(str_replace(" ", "_", $this->name));
		$this->setInterfaceClass();
		
		return parent::insert($force_validation);
	}
	
	/**
	 * Override update to allow munging of technical name and setting
	 * the FormField interface class eg FormStandardInterface 
	 * @return  boolean|array true or Array of validation errors
	 */
	public function update($force_null_values = false, $force_validation = true) {
		$this->technical_name = strtolower(str_replace(" ", "_", $this->name));
		$this->setInterfaceClass();
		
		return parent::update($force_null_values, $force_validation);
	}

	/**
	 * Look up and set the internal value for interface_class by finding 
	 * the first form interface  in configuration that responds to the
	 * type of this form field.
	 * @return 
	 */
	public function setInterfaceClass() {
		// Set interface class
		$interfaces = Config::get('form.interfaces');
		if (!empty($interfaces)) {
			foreach($interfaces as $interface) {
				if ($interface::doesRespondTo($this->type)) {
					$this->interface_class = $interface;
				}
			}
		}
	}
	
	/**
	 * Collate a list of all field types across all available form 
	 * interfaces.
	 * @return array[] eg [["Text", "text"],["Date", "date"]]
	 */
	public static function getFieldTypes() {
		$interfaces = Config::get('form.interfaces');
		$fieldTypes = [];
		if (!empty($interfaces)) {
			foreach($interfaces as $interface) {
				$fieldTypes += $interface::respondsTo();  // array append
			}
		}
		return $fieldTypes;
	}
	
	/**
	 * Load any extra data stored for this form field
	 * @return [FormFieldMetaData]
	 */
	public function getMetadata() {
		return $this->getObjects("FormFieldMetadata", ["form_field_id" => $this->id, "is_deleted" => 0]);
	}
	
	/**
	 * Generate a string representing any meta data for this form field
	 * @return string
	 */
	public function getAdditionalDetails() {
		$metadata = $this->getMetadata();
		$additional_details = '';
		if (!empty($metadata)){
			foreach($metadata as $meta) {
				$additional_details .= ucwords(str_replace("_", " ", $meta->meta_key)) . ": " . $meta->meta_value . ($meta !== end($metadata) ? ', ' : '');
			}
		}
		return $additional_details;
	}
	
	/**
	 * Load meta data for this form field that matches $key
	 * @return FormFieldMetadata
	 */
	public function findMetadataByKey($key) {
		$metadata = $this->getObject("FormFieldMetadata", ["meta_key" => $key, "form_field_id" => $this->id, "is_deleted" => 0]);
	}
	
	/**
	 * Return the name of this form field with spaces replaced by underscores
	 * to be used as the form reference name
	 * @return string
	 */
	public function getFormReferenceName() {
		return str_replace(" ", "_", $this->name);
	}
	
	/**
	 * Build an array describing the form fields that can be used in 
	 * Html:multiColForm() containing the label, the type and the field name.
	 * @return array[]  [$title,$type,$field]
	 */
	public function getFormRow() {
		if (empty($this->type)) {
			return null;
		}
		$interface = $this->interface_class;
		$row=[
			$this->name, $interface::formType($this->type), $this->technical_name,""
		];
		$metaData=$this->getMetadata();
		if (is_array($metaData) && count($metaData)>0) {
			$metaArray=[];
			foreach ($metaData as $meta) {
				$metaArray[$meta->meta_key]=$meta->meta_value;
			}
			
			$formConfig=$interface::formConfig($this->type,$metaArray,$this->w);
			if (is_array($formConfig) && count($formConfig)>0) {
				foreach ($formConfig as $v) {
					$row[]=$v;
				}
			}
		}
		
		return $row;
	}
	
	/**
	 * Build an array describing the extra meta data form fields that can be used in 
	 * Html:multiColForm() containing the label, the type and the field name.
	 * 
	 * @return array[]  [[$title,$type,$field]]
	 */
	public function getMetaDataForm() {
		$interface = $this->interface_class;
		if ($interface::respondsTo($this->type)) {
			return $interface::metadataForm($this->type);
		}
	}
}
