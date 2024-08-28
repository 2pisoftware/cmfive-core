<?php

namespace Html\Form;

class Html5Autocomplete extends \Html\Form\InputField
{
    public $style;

    public $options;

    public function __construct($fields = [])
    {
        parent::__construct($fields);
        $this->class .= " tom-select-target";
    }

    public function __toString()
    {
        $buffer = '<input ';

        foreach (get_object_vars($this) as $field => $value) {
            if (is_null($value) || in_array($field, static::$_excludeFromOutput) || $field[0] == "_")
            {
                continue;
            }

            if ($field === "required" && ($value === true || $value === "true")) {
                $buffer .= $field . " ";
                continue;
            }

            if ($field === "options") {
                $field = "data-config";
                $value = json_encode([
                    // TODO: The old layout did this too, but it would be better to load the options from some endpoint whne required
                    // instead of just sending literally every option to the user on page load.
                    "options" => array_map(fn($val) => $this->convertOption($val), $value),
                    "maxItems" => 1,
                    "items" => $this->value,
                ]);
            }

            $buffer .= $field . "='" . $value . "' ";
        }

        return $buffer . '/>';
    }

    private function convertOption($val)
    {
        // Check for option 1
        if (is_a($val, "DbObject")) {
            return [
                "value" => $val->getSelectOptionValue(),
                "text" => $val->getSelectOptionTitle()
            ];
        } else if (is_scalar($val)) {
            return $val;
        } else {
            // Doesn't match a required format, is ignored
        }
    }
}
