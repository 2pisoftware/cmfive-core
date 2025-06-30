<?php

/**
 * This class represents a single record of information stored in a form.
 */
class FormInstance extends DbObject
{
    public $form_id;
    public $object_class;
    public $object_id;

    public $dt_created;

    /**
     * Load the object that this form instance is related to eg Task
     *
     * @return DbObject|null
     */
    public function getLinkedObject()
    {
        return $this->getObject($this->object_class, $this->object_id);
    }

    /**
     * Load the form that this form instance is related to
     *
     * @return Form|null
     */
    public function getForm()
    {
        return $this->getObject("Form", $this->form_id);
    }

    /**
     * Load the values that have been entered into all form fields
     * for this instance.
     *
     * @return FormValue[]
     */
    public function getSavedValues()
    {
        return $this->getObjects("FormValue", ["form_instance_id" => $this->id, "is_deleted" => 0]);
    }

    //returns array of values for use with generic templates. includes sub form values
    public function getValuesForGenericTemplate()
    {
        $form_values = $this->getSavedValues();
        $fields = [];
        if (!empty($form_values)) {
            foreach ($form_values as $value) {
                //functionality for sub forms
                $form_field = $value->getFormField();
                //check for specific field types
                if ($form_field->type == "subform") {
                    $field_metadata = $form_field->findMetadataByKey('associated_form');
                    if (!empty($field_metadata)) {
                        $sub_form = FormService::getInstance($this->w)->getForm($field_metadata->meta_value);
                        if (!empty($sub_form)) {
                            $sub_instances = FormService::getInstance($this->w)->getFormInstancesForFormAndObject($sub_form, $value);
                            $sub_form_data = [];
                            if (!empty($sub_instances)) {
                                foreach ($sub_instances as $sub_instance) {
                                    $sub_form_data[] = $sub_instance->getValuesForGenericTemplate();
                                }
                            }
                            $fields[$value->getFieldName()] = $sub_form_data;
                        }
                    }
                } elseif ($form_field->type == "attachment") {
                    if (!isset($fields['attachments'])) {
                        $fields['attachments'] = [];
                    }
                    $fields['attachments'][$value->getFieldName()] = FileService::getInstance($this->w)->getAttachmentsFileList($value);
                } else {
                    $fields[$value->getFieldName()] = $value->value;
                }
            }
        }
        return $fields;
    }

    /**
     * Generate the contents of a table row as HTML
     * Use a template associated with the form if available.
     * Template is parsed using system based twig parser.
     * The form values are available as template data keyed against their
     * technical names. eg <td>{{title}}</td>
     *
     * @return string
     */
    public function getTableRow()
    {
        $form_values = $this->getSavedValues();

        $form = $this->getForm();

        // If there is a row template specified then use that to display
        // The downside is that (for now) the template will need to implement its own
        // masking on values
        if (!empty($form->row_template)) {
            // Flatten the values array
            $template_data = [];
            if (!empty($form_values)) {
                foreach ($form_values as $form_value) {
                    $field = $form_value->getFormField();
                    $template_data[StringSanitiser::sanitise($field->technical_name)] = StringSanitiser::sanitise($form_value->value);
                }
            }

            return TemplateService::getInstance($this->w)->render($form->row_template, $template_data);
        }
        // NO TEMPLATE
        $table_row = '';
        $formValueCollated = [];
        // collate available form values
        if (!empty($form_values)) {
            foreach ($form_values as $value) {
                $formValueCollated[$value->form_field_id] = $value;
            }
        }

        $form_fields = $form->getFields();
        if (!empty($form_fields)) {
            foreach ($form_fields as $field) {
                if (!empty($formValueCollated[$field->id])) {
                    $table_row .= "<td class='form_instance_" . $this->id . "'>" . StringSanitiser::sanitise($formValueCollated[$field->id]->getMaskedValue()) . "</td>";
                } else {
                    $table_row .= "<td>&nbsp;</td>";
                }
            }
        }

        return $table_row;
    }

    /**
     * Return an array representing the complete structure of a form to
     * use with HtmlBootstrap5::multiColForm()
     * @return  array[]
     */
    public function getEditForm($form)
    {
        if (empty($form->id)) {
            $form = $this->getForm();
            if (empty($form->id)) {
                $form = new Form($this->w);
            }
        }

        $form_values = $this->getSavedValues();
        $form_structure = [];
        $formValueCollated = [];

        // collate available form values
        if (!empty($form_values)) {
            foreach ($form_values as $value) {
                $formValueCollated[$value->form_field_id] = $value;
            }
        }

        $form_fields = $form->getFields();
        if (!empty($form_fields)) {
            foreach ($form_fields as $field) {
                if (!empty($formValueCollated[$field->id])) {
                    $form_structure[] = [$formValueCollated[$field->id]->getFormRow()];
                } else {
                    $form_structure[] = [$field->getFormRow()];
                }
            }
        }
        
        return [StringSanitiser::sanitise($form->title) => $form_structure];
    }

    public function delete($force = false)
    {
        FormService::getInstance($this->w)->processEvents($this, 'On Deleted', $this->getForm());

        $values = $this->getSavedValues();

        if (!empty($values)) {
            foreach ($values as $value) {
                $value->delete($force);
            }
        }

        parent::delete($force);
    }

    /**
     * Can the user list this form instance
     * The following can* functions a overridden to implement the linked
     * objects own matching functions.
     *
     * The use case is, for example, if a user can view a Task but not edit
     * then those permissions are reflect in the attached form data
     *
     * @return boolean
     */
    public function canList(\User $user)
    {
        $object = $this->getLinkedObject();
        if (!empty($object->id)) {
            return $object->canList($user);
        }

        return parent::canList($user);
    }

    /**
     * Can the user view this form instance
     *
     * @return boolean
     */
    public function canView(\User $user)
    {
        $object = $this->getLinkedObject();
        if (!empty($object->id)) {
            return $object->canView($user);
        }

        return parent::canView($user);
    }

    /**
     * Can the user edit this form instance
     *
     * @return boolean
     */
    public function canEdit(\User $user)
    {
        $object = $this->getLinkedObject();
        if (!empty($object->id)) {
            return $object->canEdit($user);
        }

        return parent::canEdit($user);
    }

    /**
     * Can the user delete this form instance
     *
     * @return boolean
     */
    public function canDelete(\User $user)
    {
        $object = $this->getLinkedObject();
        if (!empty($object->id)) {
            return $object->canDelete($user);
        }

        return parent::canDelete($user);
    }
}
