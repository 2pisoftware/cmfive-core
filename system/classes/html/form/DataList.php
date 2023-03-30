<?php

namespace Html\Form;

use Html\Form\InputField;
use Html\GlobalAttributes;

class DataList extends InputField
{
    use GlobalAttributes;

    /**
     * Options should be an array of Option objects
     *
     * @var array
     */
    public $options = [];

    /**
     * Returns the datalist options
     *
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * Sets the datalist options
     *
     * @param array $options
     * @return self
     */
    public function setOptions(array $options): self
    {
        $this->options = $options;
        return $this;
    }

    /**
     * Renders the datalist
     *
     * @return string
     */
    public function __toString(): string
    {
        $datalist_id = uniqid('datalist_');

        $buffer = (new InputField([
            'id' => $this->id,
            'name' => $this->name,
            'list' => $datalist_id,
            'class' => 'form-control',
        ])) . '<datalist id="' . $datalist_id . '">';

        foreach ($this->options as $option) {
            $buffer .= $option->__toString();
        }
        
        return $buffer . '</datalist>';
    }
}
