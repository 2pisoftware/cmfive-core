<?php

function ajaxGetExtraDetails_ALL(Web $w)
{
    $w->setLayout(null);
    $p = $w->pathMatch("task_id", "task_type");
    
    if (empty($p['task_id']) || empty($p['task_type'])) {
        $w->out("[]");
        return;
    }
    
    $task = TaskService::getInstance($w)->getTask($p["task_id"]);
    if (empty($task->id)) {
        $w->out("[]");
        return;
    }
    
    $task_type = TaskService::getInstance($w)->getTaskTypeObject($p['task_type']);
    if (empty($task_type) || !method_exists($task_type, "displayExtraDetails")) {
        $w->out("[]");
        return;
    }
    
    $extraDetails = $task_type->displayExtraDetails($task);
        
    $w->out(json_encode([$extraDetails]));
}
