<?php

namespace Html\Cmfive;

use Html\Form\InputField\File;

/**
 * UI helper for enabling multiple uploaded files
 */
class MultiFileUpload extends \Html\Form\InputField\File
{
    public function __toString(): string
    {
        $buffer = '<div class="multi-upload-file-container" id="' . $this->id . '" data-name="' . $this->name . '">';
        $buffer .= '<button type="button" class="btn btn-outline-primary multi-upload-button">Add a file</button>';
        $buffer .= (new File(['class' => 'd-none multi-upload-file-element', 'multiple' => true]));
        $buffer .= (new File([
            'name' => $this->name,
            'class' => 'd-none multi-upload-files',
            'multiple' => true
        ]));
        $buffer .= '<div class="multi-upload-files-display d-none"></div>';
        $buffer .= '</div>';
        return $buffer;
    }
}
