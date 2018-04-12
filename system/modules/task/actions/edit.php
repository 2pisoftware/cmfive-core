<?php

use \Html\Form\InputField as InputField;
use \Html\Form\Select as Select;

function edit_GET(Web $w) {
    $w->setLayout('layout-f6');

    list($task_id) = $w->pathMatch("id");
    $task = (!empty($task_id) ? $w->Task->getTask($task_id) : new Task($w));
    
    // if ($task->is_deleted == 1) {
    //     $w->error('Task has been deleted',"/task/list/");
    // }
    
    // Register for timelog if not new task
    if (!empty($task->id)) {
        $w->Timelog->registerTrackingObject($task);
    }
    
    if (!empty($task->id) && !$task->canView($w->Auth->user())) {
        $w->error("You do not have permission to edit this Task", "/task/tasklist");
    }
	
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
    
    $tasktypes = array();
    $priority = array();
    $members = array();
    
    // Try and prefetch the taskgroup by given id
    $taskgroup = null;
    $taskgroup_id = $w->request("gid");
    $assigned = 0;
    if (!empty($taskgroup_id) || !empty($task->task_group_id)) {
        $taskgroup = $w->Task->getTaskGroup(!empty($task->task_group_id) ? $task->task_group_id : $taskgroup_id);
        
        if (!empty($taskgroup->id)) {
            $tasktypes = $w->Task->getTaskTypes($taskgroup->task_group_type);
            $priority = $w->Task->getTaskPriority($taskgroup->task_group_type);
            $members = $w->Task->getMembersBeAssigned($taskgroup->id);
            sort($members);
            array_unshift($members,array("Unassigned","unassigned"));
            $assigned = (empty($task->assignee_id)) ? "unassigned" : $task->assignee_id;
        }
    }

    // Add history item
    if (empty($p['id'])) {
    	History::add("New Task");
    } else {
    	History::add("Task: {$task->title}", null, $task);
    }

    $status_list = Config::get("task." . $taskgroup->task_group_type)['statuses'];
    
    $w->ctx("t", (array)$task);
    $w->ctx("task", $task);
    $w->ctx("taskgroup_list", json_encode(array_map(function($task_group) {return ['value' => $task_group->id, 'text' => $task_group->title];}, $taskgroups)));
    $w->ctx("task_type_list", json_encode(array_map(function($tasktype) {return ['value' => $tasktype, 'text' => $tasktype];}, !empty($tasktypes[0]) ? $tasktypes[0] : [])));
    $w->ctx("task_status_list", json_encode(array_map(function($status) {return ['value' => $status[0], 'text' => $status[0]];}, $status_list)));
    $w->ctx("task_priority_list", json_encode(array_map(function($p) {return ['value' => $p[0], 'text' => $p[0]];}, $priority)));
    $w->ctx("task_assignee_list", json_encode(array_map(function($assignee) {return ['value' => $assignee[1], 'text' => $assignee[0]];}, $members)));
    $w->ctx("task_assignee", $assigned);
    $w->ctx("can_i_assign", $taskgroup->getCanIAssign());
    //$w->ctx("canDelete", $task->canDelete($w->Auth->user()));
}