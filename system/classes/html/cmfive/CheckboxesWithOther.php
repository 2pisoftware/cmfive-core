<?php

namespace Html\Cmfive;

use Html\Form\CheckboxGroup;
use Html\Form\InputField;

class CheckboxesWithOther extends CheckboxGroup
{
    public $other_id;
    public $other_field;

    public static $_excludeFromOutput = [
        'other_field',
        'checkbox_values',
    ];

    public function setOtherField(InputField $other_field): self
    {
        $this->other_field = $other_field;
        return $this;
    }

    public function __toString(): string
    {
        array_push($this->checkbox_values, ['id' => $this->other_id, 'label' => 'Other', 'value' => 'other']);
        if (!empty($this->other_field)) {
            $this->other_field->setAttribute('data-other-field', $this->other_id);
            $this->other_field->class .= ' d-none';
        } else {
            return parent::__toString();
        }

        return parent::__toString() . $this->other_field->__toString();
    }
}
