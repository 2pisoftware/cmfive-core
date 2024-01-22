<?php namespace Html\Cmfive;

use Html\Form\InputField;

class Autocomplete extends InputField
{
    public string $url = '';
    public array $_lookup_values = [];

    public string $valueField = '';
    public string $labelField = '';
    public string $searchField = '';

    public $_config = [
        'create' => false,
        'maxItems' => 1,
    ];

    public function getConfig(): array
    {
        return $this->_config;
    }

    public function setConfig(array $config): self
    {
        $this->_config = $config;
        return $this;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;
        return $this;
    }

    public function setValueField(string $valueField): self
    {
        $this->valueField = $valueField;
        return $this;
    }

    public function setLabelField(string $labelField): self
    {
        $this->labelField = $labelField;
        return $this;
    }

    public function setSearchField(string $searchField): self
    {
        $this->searchField = $searchField;
        return $this;
    }

    public function setLookupValues(array $lookup_values): self
    {
        $this->_lookup_values = $lookup_values;
        return $this;
    }

    public function __toString(): string
    {
        $this->class .= ' autocomplete';
        $this->setAttribute('autocomplete', '');
        $this->setAttribute('data-config', json_encode(array_merge($this->_config, ['valueField' => $this->valueField, 'labelField' => $this->labelField, 'searchField' => $this->searchField])));
        $this->setAttribute('data-url', $this->url);
        // $this->setAttribute('data-lookup-values', json_encode($this->_lookup_values));

        return parent::__toString();
    }
}