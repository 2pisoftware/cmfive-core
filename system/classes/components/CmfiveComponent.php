<?php

abstract class CmfiveComponent
{
    public $tag = 'link';
    public $has_closing_tag = false;

    public $is_included = false;

    // The weight is an integer that allows cmfive to organise the load order of components
    // Higher numbers will appear first
    public $weight = 1000;
    public static $_excludeFromOutput = ['tag', 'has_closing_tag', 'is_included', 'weight'];

    /**
     * Returns the HTML to include this component on the page
     *
     * @return string component include code
     */
    public function _include()
    {
        if ($this->is_included) {
            return '';
        }

        $this->is_included = true;

        $buffer = '<' . $this->tag . ' ';

        foreach (get_object_vars($this) as $field => $value) {
            if (!in_array($field, static::$_excludeFromOutput) && strpos($field, '_') !== 0) {
                $buffer .= $field . ($value !== null ? '=\'' . $value . '\' ' : ' ');
            }
        }

        return $buffer . ($this->has_closing_tag ? '></' . $this->tag . '>' : '/>');
    }

    /**
     * Should return the component itself, some components may not need this like link/script components
     *
     * @return string component
     */
    public function display($binding_data = [])
    {
        return '';
    }
}
