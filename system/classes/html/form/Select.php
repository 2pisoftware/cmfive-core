<?php

namespace Html\Form;

/**
 * Class representation of a select field - HTML5 only
 *
 * Setter documentation provided from the Mozilla Developer Network
 * <https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input>
 *
 * @author Adam Buckley <adam@2pisoftware.com>
 */
class Select extends \Html\Form\FormElement
{
    use \Html\GlobalAttributes;

    public $autofocus;
    public $disabled;
    public $form;
    public $multiple;
    public $name;
    public $required;
    public $size;

    public $_selected_option;

    // Cmfive attributes
    public $options = [];

    static $_excludeFromOutput = [
        "options",
    ];

    /**
     * The options for this select class is a special case, but we want to
     * invoke the setOptions function to convert them so that the output will
     * still work.
     *
     * @param array $fields
     */
    public function __construct($fields = [])
    {
        // Check for options being set
        if (is_array($fields) && array_key_exists('options', $fields)) {
            $this->setOptions($fields['options']);
            unset($fields['options']);
        } elseif (is_object($fields)) {
            $this->setOptions($fields->options ?? []);
            $fields->options = null;
        }

        // Check for given selected option
        if (is_array($fields) && array_key_exists('selected_option', $fields)) {
            $this->setSelectedOption($fields['selected_option']);
            $this->_selected_option = $fields['selected_option'];
            unset($fields['selected_option']);
        } elseif (is_object($fields)) {
            $this->setSelectedOption($fields->selected_option ?? "");
            $fields->selected_option = null;
        }

        parent::__construct($fields);
    }

    // Cmfive setters

    /**
     * This helper function is designed to add \Html\Form\Option objects to
     * a local array to be printed via __toString().
     *
     * You can give a list of objects in multiple ways:
     * 1. An array of \Html\Form\Option objects
     * 2. An array of DbObjects
     * 3. An array with (at least) "label" and "value" keys
     * 4. The old select style, an indexed array with key 0 as the label, and
     *        key 1 as the option value
     * 5. A string, this will be used for both the option value and label so
     *        use caution!
     *
     * If the given option doesn't match one of the above use cases then it
     * will be ignored.
     *
     * @param array $options
     * @param bool $omit_deleted
     *
     * @return \Html\Form\Select this
     */
    public function setOptions($options = [], $omit_default = false)
    {
        if (!$omit_default) {
            array_push($this->options, new Option(['label' => '-- Select --', 'value' => '']));
        }

        if (!is_null($options) && is_array($options) && count($options) > 0) {
            foreach ($options as $option) {
                // Check for \Html\Form\Option
                if (is_a($option, "\Html\Form\Option")) {
                    array_push($this->options, $option);
                } elseif (is_a($option, "DbObject")) {
                    // Check for DbObject
                    array_push($this->options, new Option(["value" => $option->getSelectOptionValue(), "label" => $option->getSelectOptionTitle()]));
                } elseif (is_array($option) && count($option) >= 2) {
                    // Check for standard Option format
                    if (array_key_exists("label", $option) && array_key_exists("value", $option)) {
                        array_push($this->options, new Option($option));
                    } elseif (count($option) == 2) {
                        // Check for old (bad) style
                        array_push($this->options, new Option(["value" => $option[1], "label" => $option[0]]));
                    }
                } elseif (is_scalar($option)) {
                    // Check for string option
                    array_push($this->options, new Option(["label" => $option, "value" => $option]));
                } else {
                    // Doesn't match a required format, is ignored
                }
            }
        }

        return $this;
    }

    public function __toString()
    {
        $buffer = '<select ';

        foreach (get_object_vars($this) as $field => $value) {
            if (!is_null($value) && !in_array($field, static::$_excludeFromOutput) && $field[0] !== "_") {
                $buffer .= $field . '=\'' . $this->{$field} . '\' ';
            }
        }

        /**
         * Print the options if they exist, this assumes that the options are
         * an {@see \Html\Form\Option} object class type
         */
        $options_buffer = '';
        if (!empty($this->options)) {
            $options_buffer = implode("\n", array_map(function ($option) {
                return $option->__toString();
            }, $this->options));
        }

        return $buffer . '>' . $options_buffer . '</select>';
    }

    // HTML5 setters

    /**
     * This attribute lets you specify that a form control should have input
     * focus when the page loads, unless the user overrides it, for example by
     * typing in a different control. Only one form element in a document can
     * have the autofocus attribute, which is a Boolean.
     *
     * @param string $autofocus
     * @return \Html\Form\Select this
     */
    public function setAutofocus($autofocus)
    {
        $this->autofocus = $autofocus;

        return $this;
    }

    /**
     * This Boolean attribute indicates that the user cannot interact with the
     * control. If this attribute is not specified, the control inherits its
     * setting from the containing element, for example fieldset; if there is no
     * containing element with the disabled attribute set, then the control is
     * enabled.
     *
     * @param string $disabled
     * @return \Html\Form\Select this
     */
    public function setDisabled($disabled)
    {
        $this->disabled = $disabled;

        return $this;
    }

    /**
     * The form element that the select element is associated with (its "form
     * owner"). If this attribute is specified, its value must be the ID of a
     * form element in the same document. This enables you to place select
     * elements anywhere within a document, not just as descendants of their
     * form elements.
     *
     * @param string $form
     * @return \Html\Form\Select this
     */
    public function setForm($form)
    {
        $this->form = $form;

        return $this;
    }

    /**
     * This Boolean attribute indicates that multiple options can be selected in
     * the list. If it is not specified, then only one option can be selected at
     * a time.
     *
     * @param string $multiple
     * @return \Html\Form\Select this
     */
    public function setMultiple($multiple)
    {
        $this->multiple = $multiple;

        return $this;
    }

    /**
     * The name of the control.
     *
     * @param string $name
     * @return \Html\Form\Select this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * A Boolean attribute indicating that an option with a non-empty string
     * value must be selected.
     *
     * @param string $required
     * @return \Html\Form\Select this
     */
    public function setRequired($required)
    {
        $this->required = $required;

        return $this;
    }

    /**
     * If the control is presented as a scrolled list box, this attribute
     * represents the number of rows in the list that should be visible at one
     * time. Browsers are not required to present a select element as a scrolled
     * list box. The default value is 0.
     *
     * @param string $size
     * @return \Html\Form\Select this
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * This function is a custom Cmfive helper function designed to alter the
     * Options selected marker based on the given $option_value.
     *
     * @param string $option_value
     * @return \Html\Form\Select $this
     */
    public function setSelectedOption($option_value = null)
    {
        if (!empty($this->options) && !is_null($option_value)) {
            foreach ($this->options as &$option) {
                if ($option->value === $option_value) {
                    $option->setSelected("selected");
                    break;
                }
            }
        }

        return $this;
    }
}
