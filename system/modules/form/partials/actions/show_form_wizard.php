<?php

namespace System\Modules\Form;

function show_form_wizard(\Web $w, $params)
{
    if (!array_key_exists("form", $params)) {
        // TODO: Redirect.
        return;
    }

    $form = $params["form"];
    if (empty($form)) {
        // TODO: Redirect.
        return;
    }

    $form_fields_array = [];
    $form_fields = $form->getFields();
    foreach ($form_fields ?? [] as $form_field) {
        $form_fields_array[] = [
            "type" => $form_field->type,
            "technical_name" => $form_field->technical_name,
            "name" => $form_field->name,
            "hint" => ucfirst(str_replace("_", " ", $form_field->technical_name)),
            "value" => "",
        ];
    }

    $object_class = array_key_exists("object_class", $params) ? $params["object_class"] : "";
    $object_id = array_key_exists("object_id", $params) ? $params["object_id"] : "";

    $w->ctx("form_id", $form->id);
    $w->ctx("fields", $form_fields_array);
    $w->ctx("object_class", $object_class);
    $w->ctx("object_id", $object_id);
}
