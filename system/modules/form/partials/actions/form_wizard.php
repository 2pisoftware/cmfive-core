<?php

namespace System\Modules\Form;

function form_wizard(\Web $w, $params)
{
    \CmfiveScriptComponentRegister::registerComponent('vue-router', new \CmfiveScriptComponent('/system/templates/js/vue-router.min.js', ['weight' => 900]));
    \CmfiveScriptComponentRegister::registerComponent('ToastJS', new \CmfiveScriptComponent('/system/templates/js/Toast.js'));

    $form = $params["form"];
    if (empty($form)) {
        return;
    }

    $form_instance = new \FormInstance($w);
    $form_instance->form_id = $form->id;

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

    $w->ctx("fields", $form_fields_array);
}
