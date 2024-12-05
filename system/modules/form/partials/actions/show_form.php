<?php

function show_form(\Web $w, $params)
{
    $form = $params["form"];
    if (empty($form)) {
        return;
    }

    $form_fields = $form->getFields();
    if (empty($form_fields)) {
        return;
    }

    $form_instance = null;
    $form_instances = FormService::getInstance($w)->getFormInstancesForFormAndObject($form, $params['object']);

    // If there happens to be more than once FormInstance show the most recent one.
    if (!empty($form_instances) && count($form_instances) > 0) {
        $form_instance = array_pop($form_instances);
    }

    $table_rows = [];
    foreach ($form_fields as $form_field) {
        $form_instance_field_value = null;

        if (!empty($form_instance)) {
            $form_instance_field_value = FormService::getInstance($w)->getFormValueForInstanceAndField($form_instance->id, $form_field->id);
        }

        $table_rows[] = [$form_field->name, "static", $form_field->technical_name, empty($form_instance_field_value) ? null : $form_instance_field_value->getMaskedValue()];
    }

    $table_data[$form->title] = [
        $table_rows,
    ];

    $form_instance_id = empty($form_instance) ? "" : $form_instance->id;
    $redirect_url = array_key_exists("redirect_url", $params) ? $params["redirect_url"] : "";
    $object = array_key_exists("object", $params) ? $params["object"] : "";
    $object_class = "";

    if (!empty($object)) {
        $object_class = get_class($object);
    }

    if (array_key_exists("display_only", $params) && !$params["display_only"]) {
        $w->ctx("edit_button", HtmlBootstrap5::box("/form-instance/edit/{$form_instance_id}?form_id={$form->id}&redirect_url={$redirect_url}&object_class={$object_class}&object_id={$object->id}", "Edit", true));
    }

    $w->ctx("form", $form);
    $w->ctx("table", HtmlBootstrap5::multiColTable($table_data));
}
