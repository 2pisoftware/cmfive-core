<?php namespace Html\Form\InputField;

/**
 * A helper InputField class for datetime, to work correctly, the id attribute is
 * REQUIRED. This is not the HTML5 datetime implementation anymore as the support
 * across browsers is very poor.
 * 
 * @author Adam Buckley <adam@2pisoftware.com>
 */
class Datetime extends \Html\Form\InputField {
	
	public $type = "datetime";
	
	// public function __toString() {
	// 	$this->class .= ' date_picker';
		
	// 	$buffer = parent::__toString();
	// 	$buffer .= "<script>$('#{$this->id}').datetimepicker({ampm: true, dateFormat: 'dd/mm/yy', changeMonth: true, changeYear: true});$('#{$this->id}').keyup( function(event) { $(this).val('');}); </script>";
    //     return $buffer;
	// }
}
