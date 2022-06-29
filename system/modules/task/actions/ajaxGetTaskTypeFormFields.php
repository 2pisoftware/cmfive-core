<?php

function ajaxGetTaskTypeFormFields_GET($w) {
    $tid = Request::string('task_type');
    $tg = TaskService::getInstance($w)->getTaskGroup(Request::int('task_group_id'));
    
    if (empty($tid) or empty($tg)) {
        return;
    }
    
    $form_fields = array();
    if (!empty($tid)) {
        $form_fields = TaskService::getInstance($w)->getFormFieldsByTask($tid, $tg);
        
        if (!empty($form_fields)) {
            echo Html::form($form_fields, "/task/edit", "POST", "Save", "form_fields_form");
        }
    }
}