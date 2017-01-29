<?php namespace Html\Form\InputField;

/**
 * A helper InputField class for date, to work correctly, the id attribute is
 * REQUIRED. This is not the HTML5 date implementation anymore as the support
 * across browsers is very poor.
 * 
 * @author Adam Buckley <adam@2pisoftware.com>
 */
class Date extends \Html\Form\InputField {
	
	public $type = "text";
	
	public function __toString() {
		$this->class .= ' date_picker';
		
		$buffer = parent::__toString();
		$buffer .= "<script>$('#{$this->id}').datepicker({dateFormat: 'dd/mm/yy', changeMonth: true, changeYear: true});$('#{$this->id}').keyup( function(event) { $(this).val('');}); </script>";
        return $buffer;
	}
}
