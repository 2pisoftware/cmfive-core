<?php

function create_GET(Web $w) {
    $w->setLayout('layout-f6');
    
    // Get a list of the taskgroups and filter by what can be used
    $taskgroup_list = $w->Task->getTaskGroups();
    if (empty($taskgroup_list)) {
        if ((new Taskgroup($w))->canEdit($w->Auth->user())) {
            $w->msg('Please set up a taskgroup before continuing', '/task-group/viewtaskgrouptypes');
        } else {
            $w->error('There are no Tasks currently set up, please notify an Administrator', '/task');
        }
    }

    $taskgroups = array_filter($taskgroup_list, function($taskgroup){
        return $taskgroup->getCanICreate();
    });
    
    $tasktypes = [];
    $priority = [];
    $members = [];

    $status_list = []; //Config::get("task." . "")['statuses']
    
    $w->ctx("taskgroup_list", json_encode(array_map(function($task_group) {return ['value' => $task_group->id, 'text' => $task_group->title];}, $taskgroups)));
    $w->ctx("type_list", json_encode(array_map(function($tasktype) {return ['value' => $tasktype, 'text' => $tasktype];}, !empty($tasktypes[0]) ? $tasktypes[0] : [])));
    $w->ctx("status_list", json_encode(array_map(function($status) {return ['value' => $status[0], 'text' => $status[0]];}, $status_list)));
    $w->ctx("priority_list", json_encode(array_map(function($p) {return ['value' => $p[0], 'text' => $p[0]];}, $priority)));
    $w->ctx("assignee_list", json_encode(array_map(function($assignee) {return ['value' => $assignee[1], 'text' => $assignee[0]];}, $members)));
}