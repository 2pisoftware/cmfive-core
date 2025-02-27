<?php

namespace Html\Cmfive;

/**
 * QuillEditor wrapper for Quill, this can only be used in the new layout (layout-2021)
 * unless you manually import Quill.
 */
class QuillEditor extends \Html\Form\InputField
{
    use \Html\GlobalAttributes;

    public $options = ["theme" => "snow"];

    /**
     * Value of the textarea
     * @var string
     */
    public $value;

    /**
     * Sets the options to use for Quill
     *
     * @param array $options
     */
    public function setOptions(array $options = []): self
    {
        $this->options = array_merge($this->options, $options);
        return $this;
    }

    public function __toString()
    {
        return '<textarea name="' . $this->name . '" id="' . $this->id . '" style="display:none">' . $this->value . '</textarea><div class="quill-editor" data-quill-options=\'' . json_encode($this->options) . '\' id="quill_' . $this->id . '">' . $this->value . '</div>';
    }
}
