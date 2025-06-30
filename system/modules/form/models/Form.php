<?php

/**
 * This class represents a form that can be associated with other objects
 * in the system.
 *
 * @author Adam Buckley <adam@2pisoftware.com>
 */
class Form extends DbObject
{
    public $title;
    public $description;
    public $header_template;    // if specified this string is used as the form table header
    public $row_template;       // if specified this string is used as a template for rendering a form row
    public $summary_template;   // if specified this string is used as a template for rendering a form summary row

    /**
     * A static array of string arrays to be used for validaiton when creating forms with a Form in it.
     *
     * @var array[array[string]]
     */
    public static $_validation = [
        'title' => ['required']
    ];

    /**
     * Load the fields associated with this form
     *
     * @return array<FormField>
     */
    public function getFields()
    {
        return $this->getObjects("FormField", ["form_id" => $this->id, "is_deleted" => 0], false, true, "ordering ASC");
    }

    /**
     * Load the events associated with this form
     *
     * @return Array<FormEvent>
     */
    public function getFormEvents($type = null, $is_active = 'all')
    {
        $where = ["form_id" => $this->id, "is_deleted" => 0];
        if (!empty($type)) {
            $where['event_type'] = $type;
        }
        if ($is_active != 'all') {
            $where['is_active'] = $is_active;
        }
        return $this->getObjects("FormEvent", $where);
    }

    /**
     * Loads the unique ID field for a form if set
     *
     * @return FormField
     */
    public function getUniqueIdField()
    {
        return $this->getObject("FormField", ['form_id' => $this->id, "type" => "unique_id", "is_deleted" => 0]);
    }

    /**
     * Generate the header row for the form table
     *
     * @return string
     */
    public function getTableHeaders()
    {
        if (!empty($this->header_template)) {
            return $this->header_template;
        }

        $fields = $this->getFields();

        $header_string = '';
        if (!empty($fields)) {
            foreach ($fields as $field) {
                $header_string .= '<th>' . StringSanitiser::sanitise($field->name) . '</th>';
            }
        }

        return $header_string;
    }

    public function getTableHeadersAsArray(): array
    {
        if (!empty($this->header_template)) {
            return $this->header_template;
        }

        return array_map(fn(FormField $f) => StringSanitiser::sanitise($f->name), $this->getFields());
    }

    /**
     * Generate the summary row for the form table
     *
     * @param DbObject $object Linked object
     * @return string
     */
    public function getSummaryRow($object)
    {
        if (!empty($this->summary_template)) {
            $instances = $this->getFormInstancesForObject($object);

            // Generate a more accessible structure of the form instances and its data
            $structure = [];
            if (!empty($instances)) {
                foreach ($instances as $instance) {
                    $saved_values = $instance->getSavedValues();

                    if (!empty($saved_values)) {
                        $instance_structure = [];
                        foreach ($saved_values as $saved_value) {
                            $field = $saved_value->getFormField();
                            $instance_structure[StringSanitiser::sanitise($field->technical_name)] = StringSanitiser::sanitise($saved_value->value);
                        }
                    }

                    $structure[] = $instance_structure;
                }
            }

            return TemplateService::getInstance($this->w)->render($this->summary_template, ["form" => $structure]);
        }
        return '';
    }

    /**
     * Load the form instances containing submitted data for this form
     *
     * @return Array<FormInstance>
     */
    public function getFormInstances()
    {
        return $this->getObjects("FormInstance", ["form_id" => $this->id, "is_deleted" => 0]);
    }

    /**
     * Load a form instance based off the value of a unique identifier
     *
     * @param String $identifier_value
     */
    public function getFormInstanceByUniqueIdentifierFieldValue($identifier_value)
    {
        $unique_id_field = $this->getUniqueIdField();
        if (!empty($unique_id_field->id)) {
            // Get matching value
            $value = $this->getObject('FormValue', ['form_field_id' => $unique_id_field->id, 'value' => $identifier_value, 'is_deleted' => 0]);
            if (!empty($value->id)) {
                $form_instance = $value->getFormInstance();
                if ($form_instance->is_deleted == 0) {
                    return $form_instance;
                }
            }
        }

        return null;
    }

    /**
     * Load the form instances containing submitted data for this form
     * that are related to the $object parameter
     *
     * @param  DbObject $object Linked object
     * @return Array<FormInstance>
     */
    public function getFormInstancesForObject($object)
    {
        return FormService::getInstance($this->w)->getFormInstancesForFormAndObject($this, $object);
    }

    /**
     * Returns number of instances of submitted data attached to this form
     *
     * @param  DbObject $object Linked object
     * @return int Number of objects
     */
    public function countFormInstancesForObject($object)
    {
        return FormService::getInstance($this->w)->countFormInstancesForFormAndObject($this, $object);
    }

    /**
     * Generate label to show this record in select inputs
     *
     * @return string
     */
    public function getSelectOptionTitle()
    {
        return StringSanitiser::sanitise($this->title);
    }

    /**
     * Generate value to use for this record in select inputs
     *
     * @return string
     */
    public function getSelectOptionValue()
    {
        return $this->id;
    }

    /**
     * Generate text to show this record in search results
     *
     * @return string
     */
    public function printSearchTitle()
    {
        return StringSanitiser::sanitise($this->title);
    }

    /**
     * Generate a link to show this form
     *
     * @return string
     */
    public function printSearchUrl()
    {
        return "/form/show/" . $this->id;
    }

    public function toArray()
    {
        return [
            'title' => StringSanitiser::sanitise($this->title),
            'description' => StringSanitiser::sanitise($this->description),
            'header_template' => StringSanitiser::sanitise($this->header_template),
            'row_template' => StringSanitiser::sanitise($this->row_template),
            'summary_template' => StringSanitiser::sanitise($this->summary_template),
        ];
    }

    /**
     * Checks if this object can be listed.
     *
     * @param User $user
     * @return bool
     */
    public function canList(User $user) : bool
    {
        if (!parent::canList($user) || $this->is_deleted) {
            return false;
        }

        return true;
    }

    /**
     * Checks if this object can be viewed.
     *
     * @param User $user
     * @return bool
     */
    public function canView(User $user) : bool
    {
        if (!parent::canView($user) || $this->is_deleted) {
            return false;
        }

        return true;
    }

    /**
     * Checks if this object can be edited.
     *
     * @param User $user
     * @return bool
     */
    public function canEdit(User $user) : bool
    {
        if (!parent::canEdit($user) || $this->is_deleted) {
            return false;
        }

        return true;
    }

    /**
     * Checks if this object can be deleted.
     *
     * @param User $user
     * @return bool
     */
    public function canDelete(User $user) : bool
    {
        if (!parent::canDelete($user) || $this->is_deleted) {
            return false;
        }

        return true;
    }
}
