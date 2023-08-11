<?php namespace Html\Form\InputField;

/**
 * A helper InputField class for datetime-local, extends the datetime class for
 * the time being (as)/(in case?) browser support for datetime-local is very poor.
 * 
 * @author Adam Buckley <adam@2pisoftware.com>
 */
class DatetimeLocal extends \Html\Form\InputField\Datetime {
	
	public $type = "datetime-local";
		
}
