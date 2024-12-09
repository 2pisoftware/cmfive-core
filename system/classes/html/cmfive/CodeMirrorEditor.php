<?php

namespace Html\Cmfive;

class CodeMirrorEditor extends \Html\Form\InputField
{
    public $_config = [
            //'extensions' => ['basicSetup'],
            //'parent' => '.code-mirror-target'
    ];
    /*
    public $_config = [
        'create' => false,
        'plugins' => ['remove_button']
    ];
    */
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

    /**
     * Adds to the config used for codemirror
     *
     * @param array $config
     */
    public function addToConfig(array $config = []): self
    {
        $this->_config = array_merge($this->_config, $config);
        return $this;
    }

    public function setValues(array $values): self
    {
        $this->_values = $values;
        return $this;
    }

    public function __toString()
    {
        return '<textarea name="' . $this->name . '" id="' . $this->id . '" style="display:none"></textarea><div class="code-mirror-target" cm-value=\'' . $this->value . '\' id="' . $this->id . '">'  . '</div>';
    }
}
