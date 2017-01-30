<?php

function ajaxGetTaskTypeFormFields_GET($w) {
    $tid = $w->request('task_type');
    $tg = $w->Task->getTaskGroup($w->request('task_group_id'));
    
    if (empty($tid) or empty($tg)) {
        return;
    }
    
    $form_fields = array();
    if (!empty($tid)) {
        $form_fields = $w->Task->getFormFieldsByTask($tid,$tg);
        
        if (!empty($form_fields)) {
            echo Html::form($form_fields, "/task/edit", "POST", __("Save"), "form_fields_form");
        }
    }
}
