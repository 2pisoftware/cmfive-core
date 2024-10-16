<?php
// Create Task Group: selecting task group type automatically loads the task types

function ajaxSelectTaskGroupType_ALL(Web $w) {
    $p = $w->pathMatch("taskgrouptype");
    $taskgrouptype = $p["taskgrouptype"];

    if (empty($taskgrouptype)) {
        return;
    }

    $tasktypes = ($taskgrouptype != "") ? TaskService::getInstance($w)->getTaskTypes($taskgrouptype) : array();
    $priorities = ($taskgrouptype != "") ? TaskService::getInstance($w)->getTaskPriority($taskgrouptype) : array();
 
    // create dropdowns loaded with respective data
    $result = [
        HtmlBootstrap5::select("task_type", $tasktypes, null, "form-select"),
        HtmlBootstrap5::select("priority", $priorities, null, "form-select"),
    ];

    $w->setLayout(null);
    $w->out(json_encode($result));
}


