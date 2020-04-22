<?php

/**
 * This class represents relationships and object classes in the system
 * that are allowed to be associated with forms.
 * When you create a form, you can check mappings to enable the form on
 * a particular record page. Each checkbox corresponds to a record here.
 */
class FormMapping extends DbObject
{
    public $form_id;
    public $object;
    public $is_singleton;

    /**
     * Load the related form definition
     * @return Form|null
     */
    public function getForm()
    {
        return $this->getObject("Form", $this->form_id);
    }

    /**
     * Returns the mapping type as a string.
     *
     * @return string
     */
    public function getMappingType() : string
    {
        return $this->is_singleton ? "single" : "multiple";
    }
}
