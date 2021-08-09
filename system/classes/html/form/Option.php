<?php namespace Html\Form;

class Option extends \Html\Form\FormElement
{

    use \Html\GlobalAttributes;

    public $disabled;
    public $label;
    public $selected;
    public $value;

    static $_excludeFromOutput = [
        "label",
    ];

    public function __toString()
    {
        $buffer = '<option ';

        foreach (get_object_vars($this) as $field => $value) {
            if (!is_null($value) && !in_array($field, static::$_excludeFromOutput) && $field[0] !== "_") {
                if (is_array($this->{$field})) {
                    continue;
                }
                $buffer .= $field . '=\'' . $this->{$field} . '\' ';
            }
        }

        return $buffer . '>' . $this->label . '</option>';
    }

    /**
     * If this Boolean attribute is set, this option is not checkable. Often
     * browsers grey out such control and it won't receive any browsing event,
     * like mouse clicks or focus-related ones. If this attribute is not set,
     * the element can still be disabled if one its ancestors is a disabled
     * <optgroup> element.
     *
     * @param string $disabled
     * @return \Html\Form\Option this
     */
    public function setDisabled($disabled)
    {
        $this->disabled = $disabled;

        return $this;
    }

    /**
     * This attribute is text for the label indicating the meaning of the
     * option. If the label attribute isn't defined, its value is that of the
     * element text content.
     *
     * @param string $label
     * @return \Html\Form\Option this
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * If present, this Boolean attribute indicates that the option is initially
     * selected. If the <option> element is the descendant of a <select> element
     * whose multiple attribute is not set, only one single <option> of this
     * <select> element may have the selected attribute.
     *
     * @param string $selected
     * @return \Html\Form\Option this
     */
    public function setSelected($selected)
    {
        $this->selected = $selected;

        return $this;
    }

    /**
     * The content of this attribute represents the value to be submitted with
     * the form, should this option be selected. If this attribute is omitted,
     * the value is taken from the text content of the option element.
     *
     * @param string $value
     * @return \Html\Form\Option this
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

}
