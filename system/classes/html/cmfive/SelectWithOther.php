<?php

namespace Html\Cmfive;

use Html\Form\InputField;
use Html\Form\Option;
use Html\Form\Select;

class SelectWithOther extends Select
{
    public $other_field;

    public static $_excludeFromOutput = [
        'other_field',
        'options',
    ];

    public function setOtherField(InputField $other_field): self
    {
        $this->other_field = $other_field;
        return $this;
    }

    public function __toString(): string
    {
        $option = new Option(['label' => 'Other', 'value' => 'other']);
        if ($this->_selected_option === 'other') {
            $option->setSelected("selected");
        }
        array_push($this->options, $option);
        if (!empty($this->other_field)) {
            $this->other_field->setAttribute('data-other-field', $this->id);
            $this->other_field->class .= ' d-none';
        } else {
            return parent::__toString();
        }

        return parent::__toString() . $this->other_field->__toString();
    }
}
