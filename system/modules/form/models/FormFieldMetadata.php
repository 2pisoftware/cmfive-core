<?php

/**
 * This class represents the data stored as additional information that
 * can be entered for a form field.
 * For example a Decimal type field defines that it can store extra metadata
 * for number of places. An extra field appears in the form to enter this
 * information. The number of decimal places entered is stored in this object.
 */
class FormFieldMetadata extends DbObject
{
    public $form_field_id;  // related form field
    public $meta_key;       // meta data field name
    public $meta_value;     // stored value entered by user

    public function insert($force_validation = true)
    {
        if (is_array($this->meta_value)) {
            $this->meta_value = json_encode($this->meta_value);
        }

        parent::insert($force_validation);
    }

    public function update($force_null_values = false, $force_validation = true)
    {
        if (is_array($this->meta_value)) {
            $this->meta_value = json_encode($this->meta_value);
        }

        parent::update($force_validation);
    }
}
