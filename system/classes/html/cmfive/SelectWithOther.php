<?php

namespace Html\Cmfive;

use Html\Form\InputField;
use Html\Form\Option;
use Html\Form\Select;

class SelectWithOther extends Select
{
    public $other_field;
    public $other_target_value;

    public static $_excludeFromOutput = [
        'other_target_value',
        'other_field',
        'options',
    ];

    public function setOtherField(InputField $other_field): self
    {
        $this->other_field = $other_field;
        return $this;
    }

    public function setOtherTargetValue(string $other_target_value): self
    {
        $this->other_target_value = $other_target_value;
        return $this;
    }

    public function __toString(): string
    {
        if (is_null($this->other_target_value)) {
            $option = new Option(['label' => 'Other', 'value' => 'other']);
            if ($this->_selected_option === 'other') {
                $option->setSelected("selected");
            }
            array_push($this->options, $option);
        }
        if (!is_null($this->other_field)) {
            $this->other_field->setAttribute('data-other-field', $this->id);
            $this->other_field->setAttribute('data-other-target-value', $this->other_target_value ?? 'other');
            $this->other_field->class .= ' form-control d-none';
        } else {
            return parent::__toString();
        }

        return parent::__toString() . $this->other_field->__toString();
    }
}
