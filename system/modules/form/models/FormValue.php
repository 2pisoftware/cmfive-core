<?php

/**
 * This class stores the user entered data for a single form field.
 */
class FormValue extends DbObject
{
    public $form_instance_id;   // form record created when the use entered data
    public $form_field_id;      // form field that this record holds data for
    public $value;              // the actual value entered by the user

    /**
     * Override insert to prep fields for persistence based on field type
     * s
     * @return  boolean|array true or Array of validation errors
     */
    public function insert($force_validation = true)
    {
        $field = $this->getFormField();

        $interface = $field->interface_class;
        $this->value = $interface::modifyForPersistance($this);

        return parent::insert($force_validation);
    }

    /**
     * Override update to prep fields for persistence based on field type
     *
     * @return  boolean|array true or Array of validation errors
     */
    public function update($force_null_values = false, $force_validation = true)
    {
        $field = $this->getFormField();

        $interface = $field->interface_class;
        $this->value = $interface::modifyForPersistance($this);

        return parent::update($force_validation);
    }

    /**
     * Get the name of the form field associated with this value
     *
     * @return string
     */
    public function getFieldName()
    {
        $field = $this->getFormField();
        return $field->name;
    }

    /**
     * Get the FormField associated with this value
     *
     * @return FormField
     */
    public function getFormField()
    {
        return $this->getObject("FormField", $this->form_field_id);
    }

    /**
     * Gets the form instance
     *
     * @return FormInstance
     */
    public function getFormInstance()
    {
        return $this->getObject('FormInstance', $this->form_instance_id);
    }


    /**
     * Return an array representing a form row with the masked value
     * provided as the field data
     *
     * @return array
     */
    public function getFormRow()
    {
        $field = $this->getFormField();
        $row = $field->getFormRow();
        $value = null;

        switch ($field->type) {
            case "date":
            case "datetime":
            case "time":
                $value = $this->getMaskedValue();
                break;
            default:
                $value = $this->value;
                break;
        }

        if (count($row) == 3) {
            array_push($row, $value);
        } elseif (count($row) > 3) {
            $row = array_merge(array_slice($row, 0, 3), [$value], array_slice($row, 4));
        }
        return $row;
    }

    /**
     * Get the value of this record after modifying for display
     *
     * @return string
     */
    public function getMaskedValue()
    {
        $field = $this->getFormField();

        if (empty($field->type)) {
            return null;
        }

        $field = $this->getFormField();
        $interface = $field->interface_class;
        return $interface::modifyForDisplay($this, $this->w, $field->getMetadata());
    }
}
