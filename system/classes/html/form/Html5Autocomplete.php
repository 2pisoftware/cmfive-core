<?php

namespace Html\Form;

use LogService;

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

    /**
     * Maximum allowed selectable items. If null, no max.
     * @var number|null
     */
    public $maxItems;

    /**
     * Whether or not to allow the user to create new items
     * @var bool
     */
    public $canCreate;

    /**
     * URL to call when new items are added to the selection
     * @var string
     */
    public $onItemAdd;

    /**
     * URL to call when items are removed
     * @var string
     */
    public $onItemRemove;

    /**
     * URL to call when new items are created. Fires at the same time as onItemAdd
     * @var string
     */
    public $onItemCreate;

    public static $_excludeFromOutput = [
        "source",
        "value",
        "options",
        "onItemAdd",
        "onItemRemove",
        "onItemCreate",
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
            if (is_null($value) || in_array($field, static::$_excludeFromOutput) || $field[0] == "_") {
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
                "maxItems" => !isset($this->maxItems) ? $this->maxItems : 1,
                "items" => $this->value,
                "source" => $this->source,
                "create" => $this->canCreate,

                // for sending data to the wrapper
                "cmfive" => [
                    "onItemAdd" => $this->onItemAdd,
                    "onItemRemove" => $this->onItemRemove,
                    "onItemCreate" => $this->onItemCreate,
                ]
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
        } else if (isset($val["value"]) && isset($val["text"])) {
            return [
                "value" => $val["value"],
                "text" => $val["text"]
            ];
        } else if (is_scalar($val)) {
            return [
                "value" => $val,
                "text" => $val,
            ];
        } else {
            LogService::getInstance($this->w)->setLogger("html5autocomplete")->error("option did not match format", $val);
        }
    }
}
