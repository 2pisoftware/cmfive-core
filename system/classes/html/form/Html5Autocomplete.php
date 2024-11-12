<?php

namespace Html\Form;

use LogService;

/**
 * HTML5 Autocomplete using Tom-Select on frontend.
 * Renders an text <input> field with a dropdown for possible values,
 * which can be provided in full on render or dynamically via an API call from the frontend.
 * Frontend code: /system/templates/base/src/js/components/Autocomplete.ts
 * For example usage, see modules/task/actions/edit.php
 * or task/actions/tasklist.php
 * @author Madeline Carlier
 */

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

    /**
     * Whether or close the dropdown once a selection has been made
     * @var boolean
     */
    public $closeAfterSelect;

    /**
     * The labels for the groups (headings) of data in `options`
     * @var array<{ value: string, label: string }>
     */
    public $groups;

    /**
     *  Tom-Select plugins https://tom-select.js.org/plugins/
     * @var array<string> | object
     */
    public $plugins;

    public static $_excludeFromOutput = [
        "source",
        "options",
        "onItemAdd",
        "onItemRemove",
        "onItemCreate",
        "groups",
        "plugins"
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

            $buffer .= $field . "='" . addslashes(is_array($value) ? implode(",", $value) : $value) . "' ";
        }

        $buffer .=
            "data-config" .
            "='" .
            json_encode([
                "options" => $this->options ?
                    array_map(
                        fn($val) =>
                        array_map(
                            fn($inner) => htmlspecialchars($inner),
                            $this->convertOption($val)
                        ),
                        $this->options
                    )
                    : null,
                "maxItems" => !isset($this->maxItems) ? $this->maxItems : 1,
                "items" => $this->value,
                "source" => $this->source,
                "create" => $this->canCreate,

                "addPrecedence" => true,
                "closeAfterSelect" => $this->closeAfterSelect,

                "optgroups" => $this->groups,
                "optgroupField" => "type",

                "plugins" => $this->plugins,

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
                "text" => $val["text"],
                "type" => $val["type"],
            ];
        } else if (isset($val["id"]) && isset($val["name"])) {
            return [
                "value" => $val["id"],
                "text" => $val["name"],
            ];
        } else if (is_scalar($val)) {
            return [
                "value" => $val,
                "text" => $val,
            ];
        } else {
            // can't log cause don't have $w
            // LogService::getInstance($w)->setLogger("html5autocomplete")->error("option did not match format", $val);
            return [];
        }
    }
}
