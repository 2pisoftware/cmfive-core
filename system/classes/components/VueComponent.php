<?php

class VueComponent extends CmfiveComponent
{
    public $name;
    public $js_path;
    public $css_path;

    public function __construct($name, $js_path, $css_path = '')
    {
        $this->name = $name;
        $this->js_path = $js_path;
        $this->css_path = $css_path;
    }

    public function _include()
    {
        if ($this->is_included) {
            return '';
        }

        $this->is_included = true;
        return (!empty($this->css_path) ? '<link rel="stylesheet" href="' . $this->css_path . '" />' : '') .
            '<script src="' . $this->js_path . '"></script>';
    }

    public function display($binding_data = [])
    {
        $buffer = '<' . $this->name . ' ';

        if (!empty($binding_data)) {
            foreach ($binding_data as $field => $value) {
                $buffer .= $field . '=\'' . $value . '\' ';
            }
        }

        return $buffer . '></' . $this->name . '>';
    }
}
