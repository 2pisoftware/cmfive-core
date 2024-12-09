<?php

namespace Html\Form;

/*
 *
 * Comments provided below and available parameters for this class are provided
 * by the Mozilla Developer Network
 * <https: //developer.mozilla.org/en/docs/Web/HTML/Element/textarea>
 *
 * @author Jareem Wheeler jareem@2pisoftware.com
 */
class Banner extends \Html\Form\FormElement
{

    use \Html\GlobalAttributes, \Html\Events;

    public $name;
    public $value;

    public $type = "warning";

    static $_excludeFromOutput = ["value"];

    /**
    * Returns built string of Banner field
    *
    * @return string representation
    */
    public function __toString()
    {
        $buffer = '<div class="alert alert-' . $this->type . '" role="alert" ';

        foreach (get_object_vars($this) as $field => $value) {
            if (!is_null($value) && !is_array($this->{$field}) && !in_array($field, static::$_excludeFromOutput)) {
                $buffer .= $field . '=\'' . $this->{$field} . '\' ';
            }
        }

        return $buffer . '>' . $this->value . '</div>';
    }

    /**
     * The name of the control.
     *
     * @param string $name
     * @return \Html\Form\Banner
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * The raw value contained in the control.
     *
     * @param string $value
     * @return \Html\Form\Banner
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }
}
