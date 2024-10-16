<?php

use Html\Form\Select as Select;

function ajax_getTaskgroupDetails_GET(Web $w) {
    
    list($taskgroup_id) = $w->pathMatch();
    
    $taskgroup = TaskService::getInstance($w)->getTaskGroup($taskgroup_id);
    $taskgroup_type = $taskgroup->getTaskGroupTypeObject();
    $members = TaskService::getInstance($w)->getMembersBeAssigned($taskgroup->id);
            
    // Normalise taskgroup type fields to be consumed by the Options class
    $task_type_options_array = [];
    $task_type_array = $taskgroup_type->getTaskTypeArray();
    if (!empty($task_type_array)) {
        foreach($task_type_array as $task_type_key => $task_type_value) {
            $task_type_options_array[] = ["label" => $task_type_value, "value" => $task_type_key];
        }
    }
    
    
    $task_status_options_array = [];
    $task_status_array = $taskgroup_type->getStatusArray();
    if (!empty($task_status_array)) {
        foreach($task_status_array as $task_status_key => $task_status_value) {
            $task_status_options_array[] = ["label" => $task_status_value[0], "value" => $task_status_value[0]];
        }
    }
    
    $task_priority_options_array = [];
    $task_priority_array = $taskgroup_type->getTaskPriorityArray();
    if (!empty($task_priority_array)) {
        foreach($task_priority_array as $task_priority_value) {
            $task_priority_options_array[] = ["label" => $task_priority_value, "value" => $task_priority_value];
        }
    }
    
    $task_assignee_options_array = [];
    if (!empty($members)) {
        foreach($members as $member) {
            $task_assignee_options_array[] = ["label" => $member[0], "value" => $member[1]];
        }
    }
    
    // Build response data
    $w->setLayout(null);
    $w->out(json_encode([
        "taskgroup_description" => "Description: " . $taskgroup_type->getTaskGroupTypeDescription(),
        "taskgroup_type" => "Type: " . $taskgroup_type->getTaskgroupTypeTitle(),
        "taskgroup_type_name" => $taskgroup->task_group_type,
        "task_types" => (new Select([
                "name" => "new_task_type",
                "id" => "new_task_type"
            ]))->setOptions($task_type_options_array)->__toString(),
        "statuses" => (new Select([
                "name" => "new_task_status",
                "id" => "new_task_status"
            ]))->setOptions($task_status_options_array)->__toString(),
        "priorities" => (new Select([
                "name" => "new_task_priority",
                "id" => "new_task_priority"
            ]))->setOptions($task_priority_options_array)->__toString(),
        "assignees" => (new Select([
                "name" => "new_task_assignee",
                "id" => "new_task_assignee"
            ]))->setOptions($task_assignee_options_array)->__toString(),
    ]));
}