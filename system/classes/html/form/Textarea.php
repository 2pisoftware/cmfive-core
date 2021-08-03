<?php

namespace Html\Form;

/**
 * The HTML <textarea> element represents a multi-line plain-text editing
 * control.
 * 
 * Comments provided below and available parameters for this class are provided
 * by the Mozilla Developer Network 
 * <https://developer.mozilla.org/en/docs/Web/HTML/Element/textarea>
 * 
 * @author Adam Buckley adam@2pisoftware.com
 */
class Textarea extends \Html\Form\FormElement
{

    use \Html\GlobalAttributes, \Html\Events;

    public $autocomplete;
    public $autofocus;
    public $cols;
    public $defaultValue;
    public $disabled;
    public $form;
    public $maxlength;
    public $minlength;
    public $name;
    public $placeholder;
    public $readonly;
    public $required;
    public $rows;
    public $selectionDirection;
    public $selectionEnd;
    public $selectionStart;
    public $value;
    public $wrap;

    static $_excludeFromOutput = ["value", "label"];

    /**
     * Returns built string of textarea field
     *
     * @return string representation
     */
    public function __toString()
    {
        $buffer = '<textarea ';

        foreach (get_object_vars($this) as $field => $value) {
            if (!is_null($value) && !is_array($this->{$field}) && !in_array($field, static::$_excludeFromOutput)) {
                $buffer .= $field . '=\'' . $this->{$field} . '\' ';
            }
        }

        return $buffer . '>' . $this->value . '</textarea>';
    }

    /**
     * This attribute indicates whether the value of the control can be 
     * automatically completed by the browser. Possible values are:
     * off: The user must explicitly enter a value into this field for every 
     *		use, or the document provides its own auto-completion method; the 
     *		browser does not automatically complete the entry.
     * on: The browser can automatically complete the value based on values that
     *		the user has entered during previous uses.
     * 
     * If the autocomplete attribute is not specified on a <textarea> element,
     * then the browser uses the autocomplete attribute value of the <textarea>
     * element's form owner. The form owner is either the <form> element that 
     * this <textarea> element is a descendant of or the form element whose id 
     * is specified by the form attribute of the input element. For more 
     * information, see the autocomplete attribute in <form>.
     * 
     * @param string $autocomplete
     * @return \Html\Form\Textarea
     */
    public function setAutocomplete($autocomplete)
    {
        $this->autocomplete = $autocomplete;

        return $this;
    }

    /**
     * This Boolean attribute lets you specify that a form control should have 
     * input focus when the page loads, unless the user overrides it, for 
     * example by typing in a different control. Only one form-associated 
     * element in a document can have this attribute specified. 

     * @param string $autofocus
     * @return \Html\Form\Textarea
     */
    public function setAutofocus($autofocus)
    {
        $this->autofocus = $autofocus;

        return $this;
    }

    /**
     * The visible width of the text control, in average character widths. If it
     * is specified, it must be a positive integer. If it is not specified, the
     * default value is 20 (HTML5).

     * @param string $cols
     * @return \Html\Form\Textarea
     */
    public function setCols($cols)
    {
        $this->cols = $cols;

        return $this;
    }

    /**
     * The control's default value, which behaves like the Node.textContent 
     * property.
     * 
     * @param string $defaultValue
     * @return \Html\Form\Textarea
     */
    public function setDefaultValue($defaultValue)
    {
        $this->defaultValue = $defaultValue;

        return $this;
    }

    /**
     * This Boolean attribute indicates that the user cannot interact with the
     * control. (If this attribute is not specified, the control inherits its
     * setting from the containing element, for example <fieldset>; if there is
     * no containing element with the disabled attribute set, then the control
     * is enabled.)
     * 
     * @param string $disabled
     * @return \Html\Form\Textarea
     */
    public function setDisabled($disabled)
    {
        $this->disabled = $disabled;

        return $this;
    }

    /**
     * The form element that the <textarea> element is associated with (its
     * "form owner"). The value of the attribute must be the ID of a form
     * element in the same document. If this attribute is not specified, the
     * <textarea> element must be a descendant of a form element. This attribute
     * enables you to place <textarea> elements anywhere within a document, not
     * just as descendants of their form elements.
     * 
     * @param string $form
     * @return \Html\Form\Textarea
     */
    public function setForm($form)
    {
        $this->form = $form;

        return $this;
    }

    /**
     * The maximum number of characters (Unicode code points) that the user can
     * enter. If this value isn't specified, the user can enter an unlimited
     * number of characters.

     * @param string $maxlength
     * @return \Html\Form\Textarea
     */
    public function setMaxlength($maxlength)
    {
        $this->maxlength = $maxlength;

        return $this;
    }

    /**
     * The minimum number of characters (Unicode code points) required that the
     * user should enter.
     * 
     * @param string $minlength
     * @return \Html\Form\Textarea
     */
    public function setMinlength($minlength)
    {
        $this->minlength = $minlength;

        return $this;
    }

    /**
     * The name of the control.
     * 
     * @param string $name
     * @return \Html\Form\Textarea
     */
    public function setname($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * A hint to the user of what can be entered in the control. Carriage
     * returns or line-feeds within the placeholder text must be treated as line
     * breaks when rendering the hint.
     * 
     * @param string $placeholder
     * @return \Html\Form\Textarea
     */
    public function setPlaceholder($placeholder)
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    /**
     * This Boolean attribute indicates that the user cannot modify the value of
     * the control. Unlike the disabled attribute, the readonly attribute does
     * not prevent the user from clicking or selecting in the control. The value
     * of a read-only control is still submitted with the form.
     * 
     * @param string $readonly
     * @return \Html\Form\Textarea
     */
    public function setReadonly($readonly)
    {
        $this->readonly = $readonly;

        return $this;
    }

    /**
     * This attribute specifies that the user must fill in a value before
     * submitting a form.
     * 
     * @param string $required
     * @return \Html\Form\Textarea
     */
    public function setRequired($required)
    {
        $this->required = $required;

        return $this;
    }

    /**
     * The number of visible text lines for the control.
     * 
     * @param string $rows
     * @return \Html\Form\Textarea
     */
    public function setRows($rows)
    {
        $this->rows = $rows;

        return $this;
    }

    /**
     * The direction in which selection occurred. This is "forward" if the
     * selection was made from left-to-right in an LTR locale or right-to-left
     * in an RTL locale, or "backward" if the selection was made in the opposite
     * direction. This can be "none" if the selection direction is unknown.
     * 
     * @param string $selectionDirection
     * @return \Html\Form\Textarea
     */
    public function setSelectionDirection($selectionDirection)
    {
        $this->selectionDirection = $selectionDirection;

        return $this;
    }

    /**
     * The index to the last character in the current selection.
     * 
     * @param string $selectionEnd
     * @return \Html\Form\Textarea
     */
    public function setSelectionEnd($selectionEnd)
    {
        $this->selectionEnd = $selectionEnd;

        return $this;
    }

    /**
     * The index to the first character in the current selection.
     * 
     * @param string $selectionStart
     * @return \Html\Form\Textarea
     */
    public function setSelectionStart($selectionStart)
    {
        $this->selectionStart = $selectionStart;

        return $this;
    }

    /**
     * The raw value contained in the control.
     * 
     * @param string $value
     * @return \Html\Form\Textarea
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Indicates how the control wraps text. Possible values are:
     * hard: The browser automatically inserts line breaks (CR+LF) so that
     *		each line has no more than the width of the control; the cols
     *		attribute must be specified.
     * soft: The browser ensures that all line breaks in the value consist of a
     *		CR+LF pair, but does not insert any additional line breaks.
     * 
     * If this attribute is not specified, soft is its default value.
     * 
     * @param string $wrap
     * @return \Html\Form\Textarea
     */
    public function setWrap($wrap)
    {
        $this->wrap = $wrap;

        return $this;
    }
}
