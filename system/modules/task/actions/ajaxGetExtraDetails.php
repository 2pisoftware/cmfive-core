<?php

function ajaxGetExtraDetails_ALL(Web $w) {
    $p = $w->pathMatch("task_id", "task_type");
    
    if (empty($p['task_id']) || empty($p['task_type'])) {
        return;
    }
    
    $task = TaskService::getInstance($w)->getTask($p["task_id"]);
    if (empty($task->id)) {
        return;
    }
    
    $task_type = TaskService::getInstance($w)->getTaskTypeObject($p['task_type']);
    if (empty($task_type) || !method_exists($task_type, "displayExtraDetails")) {
        return;
    }
    
    $extraDetails = $task_type->displayExtraDetails($task);
//    $extraFormFields = $task_type->getFieldFormArray($task->_taskgroup);
    
    $w->out(json_encode(array($extraDetails))); // , Html::form($extraFormFields))));
}
