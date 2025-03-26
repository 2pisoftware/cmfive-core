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

    public $tag_allow_list = ["<h1>", "<h2>", "<h3>", "<p>", "<strong>", "<em>", "<u>", "<ol>", "<ul>", "<li>", "<a>", "<img>", "<blockquote>", "<code>", "<pre>", "<br>", "<hr>"];

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

    public function setTagAllowList(array $tag_allow_list = []): self
    {
        $this->tag_allow_list = $tag_allow_list;
        return $this;
    }

    public function __toString()
    {
        $value = strip_tags($this->value ?? '', $this->tag_allow_list);

        return '<textarea name="' . $this->name . '" id="' . $this->id . '" style="display:none">' . $value . '</textarea><div class="quill-editor" data-quill-options=\'' . json_encode($this->options) . '\' id="quill_' . $this->id . '">' . $value . '</div>';
    }
}
