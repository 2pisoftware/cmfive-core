<?php

/**
 * This class provides various lookup functions for Form object
 */
class FormService extends DbService
{
    /**
     * Load all Forms
     *
     * @return Forms[]
     */
    public function getForms()
    {
        return $this->getObjects("Form", ["is_deleted" => 0]);
    }

    /**
     * Load a form by id
     *
     * @param  mixed $id
     * @return Form|null
     */
    public function getForm($id)
    {
        return $this->getObject("Form", $id);
    }

    /**
     * Load a form field by id
     *
     * @param  mixed $id
     * @return FormField|null
     */
    public function getFormField($id)
    {
        return $this->getObject("FormField", $id);
    }

    /**
     * Load a form instance by id
     *
     * @param  mixed $id
     * @return FormInstance|null
     */
    public function getFormInstance($id)
    {
        return $this->getObject("FormInstance", $id);
    }

    /**
     * Returns a form value by id
     *
     * @param  mixed $id
     * @return FormValue|null
     */
    public function getFormValue($id)
    {
        return $this->getObject('FormValue', $id);
    }

    /**
     * Returns a form event by id
     *
     * @param  mixed $id
     * @return FormEvent|null
     */
    public function getFormEvent($id)
    {
        return $this->getObject('FormEvent', ['id' => $id, 'is_deleted' => 0]);
    }

    /**
     * Returns a form event processor by id
     *
     * @param  mixed $id
     * @return FormEvent|null
     */
    public function getEventProcessor($id)
    {
        return $this->getObject('FormEventProcessor', ['id' => $id, 'is_deleted' => 0]);
    }

    /**
     * Get an array structure describing the form for use with Html:multiColForm
     *
     * @return array
     */
    public function buildForm(FormInstance $form_instance, Form $form)
    {
        $form_structure = $form_instance->getEditForm($form);
        return $form_structure;
    }

    /**
     * get form mappings for form id.
     *
     * @return array
     */
    public function getFormMappingsForForm($form_id)
    {
        return $this->getObjects("FormMapping", ["form_id" => $form_id, "is_deleted" => 0]);
    }

    /**
     * Get an array of FormMappings for an object that inherits from DbObject.
     *
     * @param DbObject $object
     * @return array[FormMapping]
     */
    public function getFormMappingsForObject(DbObject $object)
    {
        return $this->getObjects("FormMapping", ["object" => get_class($object)]);
    }

    /**
     * Get a FormApplicationMappings for an object that inheriits from DbObject.
     *
     * @param DbObject $object
     * @return array[FormApplicationMapping]
     */
    public function getFormApplicationMappingsForObject(FormApplication $object)
    {
        return $this->getObjects("FormApplicationMapping", ["application_id" => $object->id]);
    }

    /**
     * Gets a FormMapping that is mapped to the $form & $object parameters.
     *
     * @param Form $form
     * @param string $object
     * @return FormMapping
     */
    public function getFormMapping(Form $form, string $object)
    {
        return $this->getObject("FormMapping", ["form_id" => $form->id, "object" => $object]);
    }

    /**
     * Gets a FormApplicationMapping that is mapped to the $form & $object parameters.
     *
     * @param Form $form
     * @param string $object
     * @return FormApplicationMapping
     */
    public function getFormApplicationMapping(Form $form, DbObject $object)
    {
        return $this->getObject("FormApplicationMapping", ["form_id" => $form->id, "application_id" => $object->id]);
    }

    /**
     * Check if this form is mapped to the object.
     *
     * @return boolean
     */
    public function isFormMappedToObject($form, $object)
    {
        $mapping = $this->getObject("FormMapping", ["form_id" => $form->id, "object" => $object, "is_deleted" => 0]);
        return !empty($mapping->id);
    }

    /**
     * Check if any forms are mapped to this object
     *
     * @return boolean
     */
    public function areFormsMappedToObject($object)
    {
        $mapping = $this->getObjects("FormMapping", ["object" => get_class($object), "is_deleted" => 0]);

        $application_mapping = [];
        if ($object instanceof FormApplication) {
            $application_mapping = $this->getObjects('FormApplicationMapping', ['application_id' => $object->id, 'is_deleted' => 0]);
        }

        return count($mapping) > 0 || count($application_mapping) > 0;
    }

    /**
     * Load the forms that are mapped to this object
     *
     * @return Form[]
     */
    public function getFormsMappedToObject($object)
    {
        $mapping = $this->getObjects("FormMapping", ["object" => get_class($object), "is_deleted" => 0]);
        $forms = [];
        if (!empty($mapping)) {
            foreach ($mapping as $map) {
                $forms[] = $map->getForm();
            }
        }

        $application_forms = [];
        if ($object instanceof FormApplication) {
            $application_forms = $this->getObjects('FormApplicationMapping', ['application_id' => $object->id, 'is_deleted' => 0]);
            if (!empty($application_forms)) {
                foreach ($application_forms as $application_form) {
                    $form = $application_form->getForm();

                    if (!empty($form)) {
                        $forms[] = $form;
                    }
                }
            }
        }

        return $forms;
    }

    /**
     * Load the form instances for a given form and object
     *
     * @return FormInstance[]
     */
    public function getFormInstancesForFormAndObject($form, $object, $page = null, $pagesize = null)
    {
        return $this->getObjects("FormInstance", ["form_id" => $form->id, "object_class" => get_class($object), "object_id" => $object->id, "is_deleted" => 0], false, true, null, $page, $pagesize);
    }

    /**
     * Counts the number of form instances attached to an object
     *
     * @param Form $form
     * @param DbObject $object
     * return int
     */
    public function countFormInstancesForFormAndObject($form, $object)
    {
        return $this->w->db->get('form_instance')->where("form_id", $form->id)
            ->where("object_class", get_class($object))->where("object_id", $object->id)
            ->where("is_deleted", 0)->count();
    }

    /**
     * Load a form field by form and field name
     *
     * @return FormField
     */
    public function getFormFieldByFormIdAndTitle($form_id, $name)
    {
        return $this->getObject("FormField", ["form_id" => $form_id, "technical_name" => $name, "is_deleted" => 0]);
    }

    /**
     * Load an Application
     *
     * @param unknown $application_id
     */
    public function getFormApplication($application_id)
    {
        return $this->getObject("FormApplication", ["id" => $application_id, "is_deleted" => 0]);
    }

    public function getFormValueForInstanceAndField($instance_id, $field_id)
    {
        return $this->getObject('FormValue', ['form_instance_id' => $instance_id, 'form_field_id' => $field_id, 'is_deleted' => 0]);
    }

    //retrun all form applications
    public function getFormApplications()
    {
        return $this->getObjects('FormApplication', ['is_deleted' => 0]);
    }

    //return form applications mapped to form
    public function getFormApplicationsForFormId($form_id)
    {
        $mappings = $this->getFormApplicationMappingForFormId($form_id);
        $results = [];
        if (!empty($mappings)) {
            foreach ($mappings as $map) {
                $results[] = $this->getFormApplication($map->application_id);
            }
        }
        return $results;
    }

    //return form application mappings for form id
    public function getFormApplicationMappingForFormId($form_id)
    {
        return $this->getObjects('FormApplicationMapping', ['form_id' => $form_id]);
    }

    /**
     * Checks imported form title and updates to remove duplications
     *
     * @param string $form_title
     * @param int
     *
     * @return string
     */
    public function checkImportedFormTitle($form_title, $number = 0)
    {
        if ($number == 0 && ($this->getFormByTitle($form_title)) || $this->getFormByTitle($form_title . ' (' . $number . ')')) {
            $number += 1;
            return $this->checkImportedFormTitle($form_title, $number);
        } else {
            if ($number > 0) {
                $form_title .= ' (' . $number . ')';
            }
            return $form_title;
        }
    }

    /**
     * Checks imported application title and updates to remove duplications
     *
     * @param string $form_title
     * @param int
     *
     * @return string
     */
    public function checkImportedApplicationTitle($app_title, $number = 0)
    {
        if ($number == 0 && ($this->getApplicationByTitle($app_title)) || $this->getApplicationByTitle($app_title . ' (' . $number . ')')) {
            $number += 1;
            return $this->checkImportedApplicationTitle($app_title, $number);
        } else {
            if ($number > 0) {
                $app_title .= ' (' . $number . ')';
            }
            return $app_title;
        }
    }
    public function getApplicationByTitle($app_title)
    {
        return $this->getObjects('FormApplication', ['title' => $app_title, 'is_deleted' => 0]);
    }

    public function getFormByTitle($form_title)
    {
        return $this->getObjects('Form', ['title' => $form_title, 'is_deleted' => 0]);
    }

    /**
     * loops through form parmaeters and returns array
     *
     * @param form_id
     * @return array
     */
    public function getFormForExport($form_id)
    {
        $export = [];
        $form = $this->getForm($form_id);
        if (!empty($form)) {
            $export = [
                "form_title" => $form->title,
                "description" => $form->description,
                "header_template" => $form->header_template,
                "row_template" => $form->row_template,
                "summary_template" => $form->summary_template,
                "form_fields" => [],
                "form_mappings" => []
            ];
            $form_fields = $form->getFields();
            if (!empty($form_fields)) {
                $fields = [];
                foreach ($form_fields as $form_field) {
                    $field = [
                        "field_name" => $form_field->name,
                        "technical_name" => $form_field->technical_name,
                        "interface_class" => $form_field->interface_class,
                        "type" => $form_field->type,
                        "mask" => $form_field->mask,
                        "ordering" => $form_field->ordering,
                        "field_metadata" => []
                    ];
                    $field_metadata = $form_field->getMetadata();
                    if (!empty($field_metadata)) {
                        $fmd_array = [];
                        foreach ($field_metadata as $field_md) {
                            $md_array = [
                                "meta_key" => $field_md->meta_key,
                                "meta_value" => $field_md->meta_value
                            ];
                            //if meta key = associated form add the form to the array
                            if ($field_md->meta_key == 'associated_form') {
                                $md_array["sub_form"] = $this->getFormForExport($field_md->meta_value);
                            }
                            $fmd_array[] = $md_array;
                        }
                        $field['field_metadata'] = $fmd_array;
                    }
                    $fields[] = $field;
                }
                $export['form_fields'] = $fields;
            }
            //copy form mapping
            $form_mappings = $this->getFormMappingsForForm($form->id);
            if (!empty($form_mappings)) {
                $mappings = [];
                foreach ($form_mappings as $mapping) {
                    $mappings[] = $mapping->object;
                }
                $export['form_mappings'] = $mappings;
            }
        }
        return $export;
    }

    /**
     * imports a form and sub forms
     *
     *
     */
    public function importForm($form_title, $form_array)
    {
        //check for form title override and check title
        $form_title = $this->checkImportedFormTitle($form_title);
        $new_form = new Form($this->w);
        $new_form->title = $form_title;
        $new_form->description = $form_array->description;
        $new_form->header_template = $form_array->header_template;
        $new_form->row_template = $form_array->row_template;
        $new_form->summary_template = $form_array->summary_template;
        $new_form->insert();

        //set up the form fields
        if (!empty($form_array->form_fields)) {
            foreach ($form_array->form_fields as $field) {
                $new_field = new FormField($this->w);
                $new_field->form_id = $new_form->id;
                $new_field->name = $field->field_name;
                $new_field->technical_name = $field->technical_name;
                $new_field->interface_class = $field->interface_class;
                $new_field->type = $field->type;
                $new_field->mask = $field->mask;
                $new_field->ordering = $field->ordering;
                $new_field->insert();
                //set up field metadata
                if (!empty($field->field_metadata)) {
                    foreach ($field->field_metadata as $metadata) {
                        $new_metadata = new FormFieldMetadata($this->w);
                        $new_metadata->form_field_id = $new_field->id;
                        $new_metadata->meta_key = $metadata->meta_key;
                        $new_metadata->meta_value = $metadata->meta_value;
                        $new_metadata->insert();
                        //check if field is sub form
                        if ($metadata->meta_key == 'associated_form') {
                            $sub_form = $this->importForm($metadata->sub_form->form_title, $metadata->sub_form);
                            $new_metadata->meta_value = $sub_form->id;
                            $new_metadata->update();
                        }
                    }
                }
            }
        }

        //set up the form mapping
        if (!empty($form_array->form_mappings)) {
            foreach ($form_array->form_mappings as $mapping) {
                $new_mapping = new FormMapping($this->w);
                $new_mapping->form_id = $new_form->id;
                $new_mapping->object = $mapping;
                $new_mapping->insert();
            }
        }

        return $new_form;
    }

    /**
     * Returns a parsed list of available event processors
     * @return array list
     */
    public function getEventProcessorList()
    {
        // Get Modules => Processor list
        $list = [];
        foreach ($this->w->modules() as $module) {
            $processors = Config::get("{$module}.form_event_processors");
            if (!empty($processors)) {
                foreach ($processors as $processor) {
                    $list[] = $module . "." . $processor;
                }
            }
        }

        return $list;
    }

    /**
     * Saves a form and returns the instance
     * @return FormInstance
     */
    public function saveForm($form_id, $field_values, $file_values, $form_instance_id = null, $object_class = null, $object_id = null)
    {
        $instance = null;
        $form = null;
        if (!empty($form_instance_id)) {
            $instance = FormService::getInstance($this->w)->getFormInstance($form_instance_id);
            $form = $instance->getForm();
        } else {
            $form = FormService::getInstance($this->w)->getForm($form_id);
            $instance = new FormInstance($this->w);
            $instance->form_id = $form_id;
        }

        $instance->object_class = $object_class;
        $instance->object_id = $object_id;
        $instance->insertOrUpdate();

        // Get existing values to update
        $instance_values = $instance->getSavedValues();
        if (!empty($instance_values)) {
            foreach ($instance_values as $instance_value) {
                $field = $instance_value->getFormField();

                if (array_key_exists($field->technical_name, $field_values)) {
                    $instance_value->value = $field_values[$field->technical_name];
                    $instance_value->update();
                    unset($field_values[$field->technical_name]);
                } elseif (array_key_exists($field->technical_name, $file_values)) {
                    // Used for attachment field types
                    // Trigger update to allow the modifyForPersistance to take care of attachment uploads
                    $instance_value->update();
                    unset($file_values[$field->technical_name]);
                } elseif ($field->type === "boolean") {
                    $instance_value->value = "0";
                    $instance_value->update();
                } else {
                    $instance_value->delete();
                }
            }
        }

        // Add new POST values
        if (!empty($field_values)) {
            foreach ($field_values as $key => $value) {
                $field = FormService::getInstance($this->w)->getFormFieldByFormIdAndTitle($form->id, $key);
                // if post variables don't match form fields, ignore them
                if (!empty($field)) {
                    $instance_value = new FormValue($this->w);
                    $instance_value->form_instance_id = $instance->id;
                    $instance_value->form_field_id = $field->id;
                    $instance_value->value = $value;
                    $instance_value->insert();
                }
            }
        }

        // Add new FILE values
        if (!empty($file_values)) {
            foreach ($file_values as $key => $value) {
                $field = FormService::getInstance($this->w)->getFormFieldByFormIdAndTitle($form->id, $key);
                // if post variables don't match form fields, ignore them
                if (!empty($field)) {
                    $instance_value = new FormValue($this->w);
                    $instance_value->form_instance_id = $instance->id;
                    $instance_value->form_field_id = $field->id;
                    $instance_value->value = ''; // Attachment types will set the value in the Interface
                    $instance_value->insert();
                }
            }
        }

        //run 'on created' or 'on modified' processors here
        if (!empty($form_instance_id)) {
            //run 'on modified processor'
            $this->processEvents($instance, 'On Modified', $form);
        } else {
            //run 'on created processor'
            $this->processEvents($instance, 'On Created', $form);
        }

        return $instance;
    }

    public function processEvents($form_instance, $event_type, $form)
    {
        $events = $form->getFormEvents($event_type, 1);
        if (!empty($events)) {
            foreach ($events as $event) {
                //check if event has application set or matching
                if (empty($event->form_application_id) || ($form_instance->object_class = "FormApplication" && $event->form_application_id == $form_instance->object_id)) {
                    $processor_class = $event->retrieveProcessor();
                    if (!empty($processor_class)) {
                        $processor_class->process($event, $form_instance);
                    }
                }
            }
        }
    }


    /**
     * Submenu navigation for Forms
     *
     * @param  Web    $w
     * @param  string $title
     * @param  array $prenav
     * @return array
     */
    public function navigation(Web $w, $title = null, $prenav = null)
    {
        if ($title) {
            $w->ctx("title", $title);
        }

        $nav = $prenav ? $prenav : [];
        if (AuthService::getInstance($w)->loggedIn()) {
            $w->menuLink("form-application", "Applications", $nav);
            $w->menuLink("form", "Forms", $nav);
        }

        return $nav;
    }

    public function navList(): array
    {
        return [
            new MenuLinkStruct("Applications", "form-application"),
            new MenuLinkStruct("Forms", "form"),
        ];
    }
}
