<?php
// Create Task Group: selecting task group type automatically loads the task types

function ajaxSelectTaskGroupType_ALL(Web $w) {
    $p = $w->pathMatch("taskgrouptype");
    $taskgrouptype = $p["taskgrouptype"];

    if (empty($taskgrouptype)) {
        return;
    }

    $tasktypes = ($taskgrouptype != "") ? $w->Task->getTaskTypes($taskgrouptype) : array();
    $priorities = ($taskgrouptype != "") ? $w->Task->getTaskPriority($taskgrouptype) : array();
 
    // create dropdowns loaded with respective data
    $result = array();
    $result[] = Html::select("task_type",$tasktypes,null);
    $result[] = Html::select("priority",$priorities,null);

    $w->setLayout(null);
    $w->out(json_encode($result));
}


