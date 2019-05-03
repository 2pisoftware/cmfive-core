<?php namespace Html\Form;

/**
 * Class representation of an input field - HTML5 only
 * 
 * Default type is "text", setter documentation provided from the Mozilla
 * Developer Network <https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input>
 * 
 * @author Adam Buckley <adam@2pisoftware.com>
 * @see \Html\GlobalAttribtues
 */
class InputField extends \Html\Form\FormElement {
	
	use \Html\GlobalAttributes, \Html\Events;
	
    public $accept;
	public $autocomplete;
    public $autofocus;
	public $autosave;
	public $checked;
	public $disabled;
	public $form;
	public $formaction;
	public $formenctype;
	public $formmethod;
	public $formnovalidate;
	public $formtarget;
	public $height;
	public $inputmode;
	public $list;
	public $max;
	public $maxlength;
	public $min;
	public $minlength;
	public $multiple;
	public $name;
	public $pattern;
	public $placeholder;
	public $readonly;
	public $required;
	public $selectionDirection;
	public $size;
	public $src;
	public $step;
	public $usemap;
	public $value;
	public $width;
	
	// Default to text input field
    public $type = 'text';
	
	/**
	 * This constructor will take an associative array where the keys match any
	 * field given above and set the field value to the corresponding array
	 * key value (implementation in parent).
	 * 
	 * @param Array $fields
	 */
	public function __construct($fields = []) {
		// Set default GlobalAttribute's $class
		$this->setClass("small-12 columns");
	
		parent::__construct($fields);
	}
	
	/**
	 * Returns built string of input field
	 * 
	 * @return string string representation
	 */
	public function __toString() {
		$buffer = '<input ';

		foreach(get_object_vars($this) as $field => $value) {
			if (!is_null($value) && !in_array($field, static::$_excludeFromOutput)) {
				$buffer .= $field . '=\'' . $value . '\' ';
			}
		}

		return $buffer . '/>';
	}
	
	// A static list of labels to exclude from the output string
	public static $_excludeFromOutput = [
		"label", "_typeList"
	];
	
	// A static list of all possible values for an Input field
	public static $_typeList = [
		"button" => "button",
		"checkbox" => "checkbox",
		"color" => "color",						// HTML5
		"colour" => "color",					// HTML5
		"date" => "date",						// HTML5
		"datetime" => "datetime",				// HTML5
		"datetime-local" => "datetime-local",	// HTML5
		"email" => "email",						// HTML5
		"file" => "file",
		"hidden" => "hidden",
		"image" => "image",
		"month" => "month",						// HTML5
		"number" => "number",					// HTML5
		"password" => "password",
		"radio" => "radio",
		"range" => "range",						// HTML5
		"reset" => "reset",
		"search" => "search",					// HTML5
		"submit" => "submit",
		"tel" => "tel",							// HTML5
		"text" => "text",
		"time" => "time",						// HTML5
		"url" => "url",							// HTML5
		"week" => "week"						// HTML5
	];
	
	// HTML5 Setters
	
	/**
	 * If the value of the type attribute is file, this attribute indicates the 
	 * types of files that the server accepts; otherwise it is ignored. The 
	 * value must be a comma-separated list of unique content type specifiers:
	 * 
	 * A file extension starting with the STOP character (U+002E). (E.g.: ".jpg,.png,.doc")
	 * A valid MIME type with no extensions
	 * audio/* representing sound files HTML5
	 * video/* representing video files HTML5
	 * image/* representing image files HTML5
	 * 
	 * @param Mixed accept
	 * @return \Html\Form\InputField this
	 */
	public function setAccept($accept) {
		$this->accept = $accept;

		return $this;
	}
	
	/**
	 * This attribute indicates whether the value of the control can be 
	 * automatically completed by the browser.
	 * 
	 * Possible values are: off, on, name, honorific-prefix, given-name, 
	 * additional-name, family-name, honorific-suffix, nickname, email, username,
	 * new-password, current-password, organization-title, organization, 
	 * street-address, address-line1, address-line2, address-line3, 
	 * address-level4, address-level3, address-level2, address-level1, country,
	 * country-name, postal-code, cc-name, cc-given-name, cc-additional-name,
	 * cc-family-name, cc-number, cc-exp, cc-exp-month, cc-exp-year, cc-csc,
	 * cc-type, transaction-currency, transaction-amount, language, bday, 
	 * bday-day, bday-month, bday-year, sex, tel, url, photo
	 * 
	 * @param Mixed autocomplete
	 * @return \Html\Form\InputField this
	 */
	public function setAutocomplete($autocomplete) {
		$this->autcomplete = $autocomplete;
		
		return $this;
	}
	
	/**
	 * This Boolean attribute lets you specify that a form control should have 
	 * input focus when the page loads, unless the user overrides it, for 
	 * example by typing in a different control. Only one form element in a 
	 * document can have the autofocus attribute, which is a Boolean. It cannot 
	 * be applied if the type attribute is set to hidden (that is, you cannot 
	 * automatically set focus to a hidden control). Note that the focusing of 
	 * the control may occur before the firing of the DOMContentLoaded event.
	 * 
	 * @param Mixed $autofocus
	 * @return \Html\Form\InputField this
	 */
	public function setAutofocus($autofocus) {
		$this->autofocus = (bool) $autofocus;
		
		return $this;
	}
	
	/**
	 * This attribute should be defined as a unique value. If the value of the 
	 * type attribute is search, previous search term values will persist in the 
	 * dropdown across page load.
	 * 
	 * @param Mixed $autosave
	 * @return \Html\Form\InputField this
	 */
	public function setAutosave($autosave) {
		$this->autosave = $autosave;
		
		return $this;
	}
	
	/**
	 * When the value of the type attribute is radio or checkbox, the presence 
	 * of this Boolean attribute indicates that the control is selected by 
	 * default; otherwise it is ignored.
	 * 
	 * Firefox will, unlike other browsers, by default, persist the dynamic 
	 * checked state of an <input> across page loads. Use the autocomplete 
	 * attribute to control this feature.
	 * 
	 * @param Mixed $checked
	 * @return \Html\Form\InputField this
	 */
	public function setChecked($checked) {
		$this->checked = (bool) $checked ? "checked" : null;
		
		return $this;
	}
	
	/**
	 * This Boolean attribute indicates that the form control is not available 
	 * for interaction. In particular, the click event will not be dispatched 
	 * on disabled controls. Also, a disabled control's value isn't submitted 
	 * with the form.
	 * 
	 * Firefox will, unlike other browsers, by default, persist the dynamic 
	 * disabled state of an <input> across page loads. Use the autocomplete 
	 * attribute to control this feature.
	 * 
	 * @param Mixed $disabled
	 * @return \Html\Form\InputField this
	 */
	public function setDisabled($disabled) {
		$this->disabled = (bool) $disabled ? "disabled" : null;

		return $this;
	}
	
	/**
	 * The form element that the input element is associated with (its form 
	 * owner). The value of the attribute must be an id of a <form> element in 
	 * the same document. If this attribute is not specified, this <input> 
	 * element must be a descendant of a <form> element. This attribute enables 
	 * you to place <input> elements anywhere within a document, not just as 
	 * descendants of their form elements. An input can only be associated with 
	 * one form.
	 * 
	 * @param Mixed $form
	 * @return \Html\Form\InputField this
	 */
	public function setForm($form) {
		$this->form = $form;
		
		return $this;
	}
	
	/**
	 * The URI of a program that processes the information submitted by the 
	 * input element, if it is a submit button or image. If specified, it 
	 * overrides the action attribute of the element's form owner.
	 * @param Mixed $formaction
	 * @return \Html\Form\InputField this
	 */
	public function setFormaction($formaction) {
		$this->formaction = $formaction;
		
		return $this;
	}
	
	/**
	 * If the input element is a submit button or image, this attribute 
	 * specifies the type of content that is used to submit the form to the 
	 * server. Possible values are:
	 *     application/x-www-form-urlencoded: The default value if the attribute
	 *         is not specified.
	 *     multipart/form-data: Use this value if you are using an <input> 
	 *         element with the type attribute set to file.
	 *     text/plain
	 * 
	 * If this attribute is specified, it overrides the enctype attribute of 
	 * the element's form owner.
	 * 
	 * @param Mixed $formenctype
	 * @return \Html\Form\InputField this
	 */
	public function setFormenctype($formenctype) {
		$this->formenctype = $formenctype;
		
		return $this;
	}
	
	/**
	 * If the input element is a submit button or image, this attribute 
	 * specifies the HTTP method that the browser uses to submit the form. 
	 * Possible values are:
	 *     post: The data from the form is included in the body of the form and 
	 *         is sent to the server.
	 *     get: The data from the form are appended to the form attribute URI, 
	 *		   with a '?' as a separator, and the resulting URI is sent to the 
	 *         server. Use this method when the form has no side-effects and 
	 *         contains only ASCII characters.
	 * 
	 * If specified, this attribute overrides the method attribute of the 
	 * element's form owner.
	 * 
	 * @param Mixed $formmethod
	 * @return \Html\Form\InputField this
	 */
	public function setFormmethod($formmethod) {
		$this->formmethod = $formmethod;
		
		return $this;
	}
	
	/**
	 * If the input element is a submit button or image, this Boolean attribute 
	 * specifies that the form is not to be validated when it is submitted. If 
	 * this attribute is specified, it overrides the novalidate attribute of 
	 * the element's form owner.
	 * 
	 * @param Mixed $formvalidate
	 * @return \Html\Form\InputField this
	 */
	public function setFormvalidate($formvalidate) {
		$this->formnovalidate = $formvalidate;
		
		return $this;
	}
	
	/**
	 * If the input element is a submit button or image, this attribute is a 
	 * name or keyword indicating where to display the response that is received
	 * after submitting the form. This is a name of, or keyword for, a browsing 
	 * context (for example, tab, window, or inline frame). If this attribute is
	 * specified, it overrides the target attribute of the elements's form 
	 * owner. The following keywords have special meanings:
	 *		_self: Load the response into the same browsing context as the 
	 *          current one. This value is the default if the attribute is not 
	 *			specified.
	 *		_blank: Load the response into a new unnamed browsing context.
	 *		_parent: Load the response into the parent browsing context of the 
	 *			current one. If there is no parent, this option behaves the same
	 *			way as _self.
	 *		_top: Load the response into the top-level browsing context (that 
	 *			is, the browsing context that is an ancestor of the current one,
	 *			and has no parent). If there is no parent, this option behaves 
	 *			the same way as _self.
	 * 
	 * @param Mixed $formtarget
	 * @return \Html\Form\InputField this
	 */
	public function setFormtarget($formtarget) {
		$this->formtarget = $formtarget;
		
		return $this;
	}
	
	/**
	 * If the value of the type attribute is image, this attribute defines the 
	 * height of the image displayed for the button.
	 * 
	 * @param Mixed $height
	 * @return \Html\Form\InputField this
	 */
	public function setHeight($height) {
		$this->height = $height;
		
		return $this;
	}
	
	/**
	 * A hint to the browser for which keyboard to display. This attribute 
	 * applies when the value of the type attribute is text, password, email, or
	 * url. Possible values are: verbatim, latin, latin-name, latin-prose,
	 * full-width-latin, kana, katakana, numberic, tel, email, url
	 * 
	 * @param Mixed $inputmode
	 * @return \Html\Form\InputField this
	 */
	public function setInputmode($inputmode) {
		$this->inputmode = $inputmode;
		
		return $this;
	}
	
	/**
	 * Identifies a list of pre-defined options to suggest to the user. The 
	 * value must be the id of a <datalist> element in the same document. The 
	 * browser displays only options that are valid values for this input 
	 * element. This attribute is ignored when the type attribute's value is 
	 * hidden, checkbox, radio, file, or a button type.
	 * 
	 * @param Mixed $list
	 * @return \Html\Form\InputField this
	 */
	public function setList($list) {
		$this->list = $list;
		
		return $this;
	}
	
	/**
	 * The maximum (numeric or date-time) value for this item, which must not be
	 * less than its minimum (min attribute) value.
	 * 
	 * @param Mixed $max
	 * @return \Html\Form\InputField this
	 */
	public function setMax($max) {
		$this->max = $max;
		
		return $this;
	}
	
	/**
	 * If the value of the type attribute is text, email, search, password, tel,
	 * or url, this attribute specifies the maximum number of characters (in 
	 * Unicode code points) that the user can enter; for other control types, it
	 * is ignored. It can exceed the value of the size attribute. If it is not 
	 * specified, the user can enter an unlimited number of characters. 
	 * Specifying a negative number results in the default behavior; that is,
	 * the user can enter an unlimited number of characters. The constraint is
	 * evaluated only when the value of the attribute has been changed.
	 * 
	 * @param Mixed $maxlength
	 * @return \Html\Form\InputField this
	 */
	public function setMaxlength($maxlength) {
		$this->maxlength = $maxlength;
		
		return $this;
	}
	
	/**
	 * The minimum (numeric or date-time) value for this item, which must not be
	 * greater than its maximum (max attribute) value.
	 * 
	 * @param Mixed $min
	 * @return \Html\Form\InputField this
	 */
	public function setMin($min) {
		$this->min = $min;
		
		return $this;
	}
	
	/**
	 * If the value of the type attribute is text, email, search, password, tel,
	 * or url, this attribute specifies the minimum number of characters (in 
	 * Unicode code points) that the user can enter; for other control types, it
	 * is ignored.
	 * 
	 * @param Mixed $minlength
	 * @return \Html\Form\InputField this
	 */
	public function setMinlength($minlength) {
		$this->minlength = $minlength;
		
		return $this;
	}
	
	/**
	 * This Boolean attribute indicates whether the user can enter more than one
	 * value. This attribute applies when the type attribute is set to email or 
	 * file; otherwise it is ignored.
	 * 
	 * @param Mixed $multiple
	 * @return \Html\Form\InputField this
	 */
	public function setMultiple($multiple) {
		$this->multiple = $multiple;
		
		return $this;
	}
	
	/**
	 * The name of the control, which is submitted with the form data.
	 * 
	 * @param Mixed $name
	 * @return \Html\Form\InputField this
	 */
	public function setName($name) {
		$this->name = $name;
		
		return $this;
	}
	
	/**
	 * A regular expression that the control's value is checked against. The 
	 * pattern must match the entire value, not just some subset. Use the title 
	 * attribute to describe the pattern to help the user. This attribute 
	 * applies when the value of the type attribute is text, search, tel, url, 
	 * email or password; otherwise it is ignored. The regular expression 
	 * language is the same as JavaScript's. The pattern is not surrounded by 
	 * forward slashes.
	 * 
	 * @param Mixed $pattern
	 * @return \Html\Form\InputField this
	 */
	public function setPattern($pattern) {
		$this->pattern = $pattern;
		
		return $this;
	}
	
	/**
	 * A hint to the user of what can be entered in the control . The 
	 * placeholder text must not contain carriage returns or line-feeds. 
	 * 
	 * @param Mixed $placeholder
	 * @return \Html\Form\InputField this
	 */
	public function setPlaceholder($placeholder) {
		$this->placeholder = $placeholder;
		
		return $this;
	}
	
	/**
	 * This Boolean attribute indicates that the user cannot modify the value of
	 * the control. It is ignored if the value of the type attribute is hidden, 
	 * range, color, checkbox, radio, file, or a button type (such as button or 
	 * submit).
	 * 
	 * @param Mixed $readonly
	 * @return \Html\Form\InputField this
	 */
	public function setReadonly($readonly) {
		$this->readonly = $readonly;
		
		return $this;
	}
	
	/**
	 * This attribute specifies that the user must fill in a value before 
	 * submitting a form. It cannot be used when the type attribute is hidden, 
	 * image, or a button type (submit, reset, or button). The :optional and 
	 * :required CSS pseudo-classes will be applied to the field as appropriate.
	 * 
	 * @param Mixed $required
	 * @return \Html\Form\InputField this
	 */
	public function setRequired($required) {
		$this->required = $required;
		
		return $this;
	}
	
	/**
	 * The direction in which selection occurred. This is "forward" if the 
	 * selection was made from left-to-right in an LTR locale or right-to-left 
	 * in an RTL locale, or "backward" if the selection was made in the opposite
	 * direction. This can be "none" if the selection direction is unknown.
	 * 
	 * @param Mixed $selectionDirection
	 * @return \Html\Form\InputField this
	 */
	public function setSelectionDirection($selectionDirection) {
		$this->selectionDirection = $selectionDirection;
		
		return $this;
	}
	
	/**
	 * The initial size of the control. This value is in pixels unless the value
	 * of the type attribute is text or password, in which case, it is an 
	 * integer number of characters. Starting in HTML5, this attribute applies 
	 * only when the type attribute is set to text, search, tel, url, email, or 
	 * password; otherwise it is ignored. In addition, the size must be greater 
	 * than zero. If you don't specify a size, a default value of 20 is used.
	 * 
	 * @param Mixed $size
	 * @return \Html\Form\InputField this
	 */
	public function setSize($size) {
		$this->size = $size;
		
		return $this;
	}
	
	/**
	 * If the value of the type attribute is image, this attribute specifies a 
	 * URI for the location of an image to display on the graphical submit 
	 * button; otherwise it is ignored.
	 * 
	 * @param Mixed $src
	 * @return \Html\Form\InputField this
	 */
	public function setSrc($src) {
		$this->src = $src;
		
		return $this;
	}
	
	/**
	 * Works with the min and max attributes to limit the increments at which a
	 * numeric or date-time value can be set. It can be the string any or a 
	 * positive floating point number. If this attribute is not set to any, the
	 * control accepts only values at multiples of the step value greater than
	 * the minimum.
	 * 
	 * @param Mixed $step
	 * @return \Html\Form\InputField this
	 */
	public function setStep($step) {
		$this->step = $step;
		
		return $this;
	}
	
	/**
	 * The type of control to display. The default type is text, if this 
	 * attribute is not specified. Possible values are: button, checkbox, color,
	 * date, datetime, datetime-local, email, file, hidden, image, month,
	 * number, password, radio, range, reset, search, submit, tel, text, time,
	 * url, week.
	 * 
	 * @param String $type
	 * @return \Html\Form\InputField this
	 */
	public function setType($type) {
		$this->type = $type;
		
		return $this;
	}
	
	/**
	 * The initial value of the control. This attribute is optional except when
	 * the value of the type attribute is radio or checkbox.
	 * 
	 * @param Mixed $value
	 * @return \Html\Form\InputField this
	 */
	public function setValue($value) {
		$this->value = $value;
		
		return $this;
	}
	
	/**
	 * If the value of the type attribute is image, this attribute defines the
	 * width of the image displayed for the button.
	 * 
	 * @param Mixed $width
	 * @return \Html\Form\InputField this
	 */
	public function setWidth($width) {
		$this->width = $width;
		
		return $this;
	}
}
    