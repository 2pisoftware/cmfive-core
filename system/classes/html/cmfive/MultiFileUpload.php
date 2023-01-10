<?php

namespace Html\Cmfive;

use Html\Form\InputField\File;
use Html\Form\InputField\Hidden;

/**
 * UI helper for enabling multiple uploaded files, all dynamic interaction
 * handled by templates/base/src/js/components/MultiFileUpload.ts
 */
class MultiFileUpload extends \Html\Form\InputField\File
{
    public $existing_files = [];

    public static $_excludeFromOutput = [
        'existing_files'
    ];

    public function getExistingFiles(): array
    {
        return $this->existing_files;
    }

    public function setExistingFiles(array $files): self
    {
        $this->existing_files = $files;
        return $this;
    }

    public function __toString(): string
    {
        $user_facing_file = (new File(['class' => 'd-none multi-upload-file-element', 'multiple' => true]));
        if (!empty($this->accept)) {
            $user_facing_file->setAccept($this->accept);
        }
        
        $buffer = '<div class="multi-upload-file-container row" id="' . $this->id . '" data-name="' . $this->name . '"><div class="' . (!empty($this->existing_files) ? 'col-sm-12 col-md-6' : 'col') . '">' .
            '<button type="button" class="btn btn-outline-primary multi-upload-button">Add a file</button>' .
            $user_facing_file .
            (new File([
                'name' => $this->name,
                'class' => 'd-none multi-upload-files',
                'multiple' => true
            ])) .
            '<div class="multi-upload-files-display d-none"></div></div>';

        // Existing files container
        if (!empty($this->existing_files)) {
            $buffer .= '<div class="col-sm-12 col-md-6 multi-upload-existing-files"><label>Existing files</label>' .
                (new Hidden(['id|name' => $this->id . '_remove']));
            foreach ($this->existing_files as $file) {
                /** @var Attachment $file */
                $buffer .= '<p data-file-id="' . $file->id . '">' . $file->filename . '<i class="bi bi-x remove float-end"></i></p>';
            }
            $buffer .= '</div>';
        }

        return $buffer . '</div>';
    }
}
