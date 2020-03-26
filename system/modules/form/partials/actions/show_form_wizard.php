<?php

namespace System\Modules\Form;

function show_form_wizard(\Web $w, $params)
{
    if (!array_key_exists("form", $params)) {
        return;
    }

    $form = $params["form"];
    if (empty($form)) {
        return;
    }

    $form_fields_array = [];
    $form_fields = $form->getFields();
    foreach ($form_fields ?? [] as $form_field) {
        $meta_data_array = [];

        if ($form_field->type === "select") {
            $meta_data = $form_field->getMetaData() ?? [];

            foreach ($meta_data as $m) {
                if ($m->meta_key === "user_rows") {
                    $meta_data_array = $m->meta_value;
                }
            }
        }

        $form_fields_array[] = [
            "type" => $form_field->type,
            "technical_name" => $form_field->technical_name,
            "name" => $form_field->name,
            "hint" => ucfirst(str_replace("_", " ", $form_field->technical_name)),
            "value" => "",
            "meta_data" => $meta_data_array,
        ];
    }

    $object_class = array_key_exists("object_class", $params) ? $params["object_class"] : "";
    $object_id = array_key_exists("object_id", $params) ? $params["object_id"] : "";

    $w->ctx("form_id", $form->id);
    $w->ctx("fields", $form_fields_array);
    $w->ctx("object_class", $object_class);
    $w->ctx("object_id", $object_id);
    $w->ctx("success_message", array_key_exists("success_message", $params) ? $params["success_message"] : "");
    $w->ctx("failure_message", array_key_exists("failure_message", $params) ? $params["failure_message"] : "");
}
