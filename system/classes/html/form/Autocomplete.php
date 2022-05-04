<?php namespace Html\Form;

/**
 * A custom Html\Form element to create an autocomplete using jQueryUI
 * This class is slightly different from convential elements as it's
 * specification doesn't come from W3C.
 *
 * @author Adam Buckley <adam@2pisoftware.com>
 */
class Autocomplete extends \Html\Form\FormElement {

	use \Html\GlobalAttributes;

	// A prefix for the autocomplete text field
	public static $_prefix = 'acp_';

	public $minlength = 3;
	public $name;
	public $options = [];
	public $required;
	public $source;
	public $value;
	public $readonly;

	public static $_excludeFromOutput = [
		'id', 'name', 'required', 'value', 'minlength', 'class', 'style',
		'options', '_prefix', 'label', 'source', 'title'
	];

	/**
	 * Sets the minlength attribute for the autocomplete. The minlength
	 * determines how many characters are needed to be typed before suggestions
	 * start appearing.
	 *
	 * Defaults to 3.
	 *
	 * @param Mixed $minlength
	 * @return \Html\Form\Autocomplete this
	 */
	function setMinlength($minlength) {
		$this->minlength = $minlength;

		return $this;
	}

	/**
	 * Sets the required string 'name' attribute.
	 * WARNING: Not setting this will break the autocomplete!
	 *
	 * @param string $name
	 * @return \Html\Form\Autocomplete this
	 */
	public function setName($name) {
		$this->name = $name;
		$this->setId($name);

		return $this;
	}

	/**
	 * Sets the options for the autocomplete, allowable array formats are:
	 * 1. Array<DbObject>
	 * 2. Array(
	 *		Array("id" => "<id>", "value" => "<value>")
	 *	  )
	 * 3. Array(
	 *		Array([0] => "<value>", [1] => "<id>")
	 *	  )
	 *    (Not recommended, is to support older formats)
	 * 4. Array("<value>")
	 *
	 * If options given do not match an above format IT WILL BE IGNORED (since
	 * there is no access to the Log mechanism and echo-ing to screen is too
	 * intrusive)
	 *
	 * @param array $options
	 * @return \Html\Form\Autocomplete this
	 */
	public function setOptions($options = [], $value_callback = null) {
		if (is_array($options) && count($options) > 0) {

			// Force $value_callback to be a Closure only
			if (!is_null($value_callback) && !is_callable($value_callback)) {
				$value_callback = null;
			}

			foreach($options as $option) {
				// Check for option 1
				if (is_a($option, "DbObject")) {
					$value = $option->getSelectOptionTitle();
					if (!is_null($value_callback)) {
						$value = $value_callback($option);
					}
					array_push($this->options, ["id" => $option->getSelectOptionValue(), "value" => $value]);
				} else if (count($option) == 2) {
					// Check for option 2
					if (array_key_exists("id", $option) && array_key_exists("value", $option)) {
						array_push($this->options, $option);
					} else {
						// Option 3 is given
						array_push($this->options, ["id" => $option[1], "value" => $option[0]]);
					}
				} else if (is_scalar ($option)) {
					// Option 4 is given
					array_push($this->options, ["id" => $option, "value" => $option]);
				} else {
					// Doesn't match a required format, is ignored
				}
			}
		}

		return $this;
	}

	/**
	 * Sets the boolean required attribute
	 *
	 * @param string $required
	 * @return \Html\Form\Autocomplete this
	 */
	public function setRequired($required) {
		$this->required = $required;

		return $this;
	}

        /**
         * Sets the boolean readonly attribute
         *
         * @param string $readonly 'true' for readonly fields
         * @return \Html\Form\Autocomplete this
         */
        public function setReadOnly($readonly) {
		$this->readonly = $readonly;

		return $this;
	}

	/**
	 * Sets the source string, this should be a url that will be used to
	 * talk via ajax to the backend. The specified datasource is required to
	 * return data in the format supported, specified here:
	 * <http://api.jqueryui.com/autocomplete/#option-source>
	 *
	 * NB: Setting the source will override any options given
	 *
	 * @param string $source
	 * @return \Html\Form\Autocomplete this
	 */
	public function setSource($source) {
		$this->source = $source;

		return $this;
	}

	/**
	 * Sets the default display title (i.e. what is displayed in the textfield)
	 * for the autocomplete
	 *
	 * @param string $title
	 * @return \Html\Form\Autocomplete this
	 */
	public function setTitle($title) {
		$this->title = $title;

		return $this;
	}

	/**
	 * Sets the default value for the autocomplete
	 *
	 * @param string $value
	 * @return \Html\Form\Autocomplete this
	 */
	public function setValue($value) {
		$this->value = $value;

		return $this;
	}

	/**
	 * To string override to print element to screen
	 *
	 * @return string
	 */
	public function __toString() {

		// Get necessary fields for HTML
		$readonly = !is_null($this->readonly) ? 'readonly="true"' : '';
		$required = !is_null($this->required) ? 'required="required"' : '';
		$source = !empty($this->source) ? '"' . $this->source . '"' : json_encode($this->options);
		$using_source = !empty($this->source) ? 'true' : 'false';
		$attribute_buffer = '';
		foreach(get_object_vars($this) as $field => $value) {
			if (!is_null($value) && !in_array($field, static::$_excludeFromOutput) && $field[0] !== "_") {
				$attribute_buffer .= $field . '=\'' . $this->{$field} . '\' ';
			}
		}

		$prefix = static::$_prefix;

		$displayValue = htmlentities($this->title ?? $this->value ?? '', ENT_QUOTES);

		return <<<BUFFER
<input type='text' style='display: none;' id='{$this->id}'  name='{$this->name}' value='{$this->value}' {$attribute_buffer} />
<div class='acp_container'>
	<input type='text' id='{$prefix}{$this->id}' name='{$prefix}{$this->name}' value='{$displayValue}' class='{$this->class}' style='{$this->style}' {$required} {$readonly} />
	<div class='circle'></div>
	<img class='center_image' width='40px' height='40px' src='/system/templates/img/cmfive_V_logo.png' />
</div>
<script type='text/javascript'>
	$(document).ready(function() {
		$('#{$prefix}{$this->id}').keyup(function(e){
			if (e.which != 13) {
				$('#{$this->id}').val('');
			}
		});

		var using_source = {$using_source};
		$('#{$prefix}{$this->id}').autocomplete({
			minLength: {$this->minlength},
			source: {$source},
			select: function(event,ui) {
				event.preventDefault();
				$('#{$this->id}').val(using_source ? ui.item.value : ui.item.id);
				$('#{$prefix}{$this->id}').val(ui.item.label);
				selectAutocompleteCallback(event, ui);
			},
			search: function() {
				$('#{$prefix}{$this->id} ~ .center_image').show();
				$('#{$prefix}{$this->id} ~ .circle').show();
				$('.ui-autocomplete').hide();
			},
			open: function() {
				$('#{$prefix}{$this->id} ~ .center_image').hide();
				$('#{$prefix}{$this->id} ~ .circle').hide();
			},
			response: function() {
				$('#{$prefix}{$this->id} ~ .center_image').hide();
				$('#{$prefix}{$this->id} ~ .circle').hide();
			}
		});
	});
</script>
BUFFER;
	}

}
