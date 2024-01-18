<?php

namespace Html\Cmfive;

use Html\Form\Select;

/**
 * A select field with autocomplete functionality
 * Implements Tom Select, similar to MultiSelect but for a single value
 * 
 * @author @tynanmatthews
 */
class Autocomplete extends Select
{

    public $_config = [
        'create' => false,
        'maxitems' => 1,
    ];
    public $_values = [];


    public function getConfig(): array
    {
        return $this->_config;
    }

    public function setConfig(array $config): self
    {
        $this->_config = $config;
        return $this;
    }

    public function setValues(array $values): self
    {
        $this->_values = $values;
        return $this;
    }

    public function __toString(): string
    {
        if (!empty($this->_values)) {
            $this->_config['items'] = $this->_values;
        }
        $this->setAttribute('data-config', json_encode($this->_config));
        $this->class = $this->class .= ' tom-select-target tom-select-autocomplete';

        return parent::__toString();
    }
}
