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
    $form_instances = $form->getFormInstances();

    if (!empty($form_instances) && count($form_instances) > 0) {
        $form_instance = $form_instances[0];
    }

    $table_rows = [];
    foreach ($form_fields as $form_field) {
        $form_instance_field_value = $w->Form->getFormValueForInstanceAndField($form_instance->id, $form_field->id);
        $table_rows[] = [$form_field->name, "static", $form_field->technical_name, empty($form_instance_field_value) ? null : $form_instance_field_value->value];
    }

    $table_data[$form->title] = [
        $table_rows,
    ];

    $form_instance_id = empty($form_instance) ? "" : $form_instance->id;
    $redirect_url = $params["redirect_url"] ?? "";
    $object = $params["object"] ?? "";

    $edit_button = Html::box("/form-instance/edit/{$form_instance_id}?form_id={$form->id}&redirect_url={$redirect_url}&object_class={get_class($object)}&object_id={$object->id}", "Edit", true);
    $delete_button = Html::b("/form-instance/delete/{$form_instance_id}?redirect_url={$redirect_url}", "Delete", "Are you sure you want to delete this " . $form->title . "?", null, false, "alert");

    $w->ctx("form", $form);
    $w->ctx("edit_button", $edit_button);
    $w->ctx("delete_button", $delete_button);
    $w->ctx("table", Html::multiColTable($table_data));
}
