<?php

namespace System\Modules\Form;

function form_wizard(\Web $w, $params)
{
    $form = $params["form"];
    if (empty($form)) {
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

    $w->ctx("form_id", $form->id);
    $w->ctx("fields", $form_fields_array);
    $w->ctx("object_class", $params["object_class"]);
    $w->ctx("object_id", $params["object_id"]);
}
