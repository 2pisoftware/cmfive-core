<?php

namespace Html;

use CmfiveScriptComponent;
use CmfiveScriptComponentRegister;
use Html\Element;
use Html\GlobalAttributes;

class VueComponent extends Element
{
    use GlobalAttributes;

    // Needed for compatibility with multiColForm
    public $label;
    public $required;
    private $component_name;
    private static $_excludeFromOutput = [
        'component_name'
    ];

    public function __construct($name, $fields = [])
    {
        CmfiveScriptComponentRegister::registerComponent('vue_' . $name, new CmfiveScriptComponent('/system/templates/base/dist/components/' . $name . '.js'));
        $this->component_name = $name;
        parent::__construct($fields);
    }

    public function __toString()
    {
        $buffer = "<{$this->component_name} ";
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

        return $buffer . "></{$this->component_name}>";
    }
}
