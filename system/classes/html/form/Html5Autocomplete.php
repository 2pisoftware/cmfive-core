<?php

namespace Html\Form;

class Html5Autocomplete extends \Html\Form\InputField
{
    public $style;

    /**
     * If specified, directly provides options for TomSelect
     * @var array<\DbObject|string>
     */
    public $options;

    /**
     * If specified, is used as the endpoint for collecting more options
     * @var string
     */
    public $source;

    public static $_excludeFromOutput = [
        "source",
        "value",
        "options",
    ];

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

            $buffer .= $field . "='" . $value . "' ";
        }

        $buffer .=
            "data-config" .
            "='" .
            json_encode([
                "options" => $this->options ? array_map(fn($val) => $this->convertOption($val), $this->options) : null,
                "maxItems" => 1,
                "items" => $this->value,
                "source" => $this->source
            ]) .
            "' ";

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
