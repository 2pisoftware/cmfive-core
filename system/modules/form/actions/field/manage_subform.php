<?php

function manage_subform_GET(Web $w)
{
    list($form_value_id) = $w->pathMatch('id');

    if (empty($form_value_id)) {
        $w->ctx('error_message', 'No ID given');
    }

    $form_value = FormService::getInstance($w)->getFormValue($form_value_id);
    if (empty($form_value->id)) {
        $w->ctx('error_message', 'Subform not found');
    }

    // Check that value is a subform
    $field = $form_value->getFormField();
    if ($field->type !== "subform") {
        $w->out("Subform not found");
        return;
    }

    // Check that metadata is found.
    $metadata = $field->getMetadata();
    if (empty($metadata)) {
        $w->out("Subform not found");
        return;
    }

    $subform = null;
    foreach ($metadata as $metadata_row) {
        if ($metadata_row->meta_key === "associated_form") {
            $subform = FormService::getInstance($w)->getForm($metadata_row->meta_value);
        }
    }

    // Check that subform is found.
    if (empty($subform)) {
        $w->out("Subform not found");
        return;
    }

    $is_singleton = false;
    $form_instance = $form_value->getFormInstance();

    // Check if the Form has a singleton mapping.
    if (!empty($form_instance)) {
        $form = $form_instance->getForm();

        if (!empty($form)) {
            $form_mapping = FormService::getInstance($w)->getFormMapping($form, $form_instance->object_class);
            if (!empty($form_mapping) && $form_mapping->is_singleton) {
                $is_singleton = true;
            }
        }
    }

    $w->ctx('subform', $subform);
    $w->ctx("form_value", $form_value);
    $w->ctx('display_only', !!Request::bool('display_only'));
    $w->ctx("is_singleton", $is_singleton);
}
