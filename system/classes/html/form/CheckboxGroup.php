<?php

namespace Html\Form;

use Html\Form\InputField\Checkbox;

class CheckboxGroup extends InputField
{
    public $checkbox_values = [];
    public $selected_values = [];

    public function setCheckboxValues(array $checkbox_values, array $selected_values)
    {
        $this->checkbox_values = $checkbox_values;
        $this->selected_values = $selected_values;
        return $this;
    }

    public static $_excludeFromOutput = [
        'checkbox_values',
        'selected_values'
    ];

    public function __toString(): string
    {
        $buffer = '';
        foreach ($this->checkbox_values as $checkbox_value) {
            $checkbox_value['name'] = $this->name;
            $checkbox_value['class'] = 'form-check-input';

            $checkbox = new Checkbox($checkbox_value);
            if (in_array($checkbox->value, $this->selected_values)) {
                $checkbox->setChecked(true);
            }

            $buffer .= '<div class="form-check"> ' . $checkbox->__toString() . '<label class="form-check-label"' . (!empty($checkbox->id) ? ' for="' . $checkbox->id . '"' : '') . '>' . $checkbox->label . '</label></div>';
        }

        return $buffer;
    }
}
