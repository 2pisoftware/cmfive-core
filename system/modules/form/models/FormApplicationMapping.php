<?php

/**
 * This class represents which Forms are mapped to which
 * distinct FormApplications. When you create an application
 * you can choose from all forms which are mapped to the
 * FormApplication class.
 */
class FormApplicationMapping extends DbObject
{
    public $form_id;
    public $application_id;
    public $is_singleton;

    /**
     * Load the related form definition
     * @return Form|null
     */
    public function getForm()
    {
        return $this->getObject("Form", $this->form_id);
    }

    public function getApplication()
    {
        return $this->getObject("FormApplication", $this->application_id);
    }
}
