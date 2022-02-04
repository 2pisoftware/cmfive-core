<?php

/**
 * FormApplicationView defines a template which can display the data of all the
 * forms for this application in a completely configurable way.
 * 
 * @author Carsten Eckelmann <carsten@2pisoftware.com>
 */
class FormApplicationView extends DbObject
{
	public $application_id;
	public $form_id; // if null, then load all form instances into the template
	public $title;
	public $description;
	public $template_id;

	function getForm()
	{
		return FormService::getInstance($this->w)->getForm($this->form_id);
	}

	function getApplication()
	{
		return FormService::getInstance($this->w)->getFormApplication($this->application_id);
	}

	/**
	 * loads the data and applies the template to it. Then
	 * returns the template outpout as a string.
	 */
	function toString()
	{
		if (!empty($this->template)) {

			if (!empty($form_id)) {
				$instances = $this->getForm()->getFormInstancesForObject($this->getApplication());
			}

			// Generate a more accessible structure of the form instances and its data
			$structure = [];
			if (!empty($instances)) {
				foreach ($instances as $instance) {
					$saved_values = $instance->getSavedValues();

					if (!empty($saved_values)) {
						$instance_structure = [];
						foreach ($saved_values as $saved_value) {
							$field = $saved_value->getFormField();
							$instance_structure[$field->technical_name] = $saved_value->value;
						}
					}

					$structure[] = $instance_structure;
				}
			}

			return TemplateService::getInstance($this->w)->render($this->template, ["form" => $structure]);
		}
		return '';
	}
}
