<?php
function edit_GET(Web $w) {
    $w->setLayout('layout-f6');
    $p = $w->pathMatch("id");
    $task = (!empty($p["id"]) ? $w->Task->getTask($p["id"]) : new Task($w));

    if (empty($task)) {
        $w->error('Task not found', '/task/list');
    }
    // Register for timelog if not new task
    $w->Timelog->registerTrackingObject($task);

    if (!$task->canView($w->Auth->user())) {
        $w->error("You do not have permission to edit this Task", "/task/list");
    }

    // Get a list of the taskgroups and filter by what can be used
    $taskgroups = $w->Task->getTaskGroups();
    if (empty($taskgroups)) {
        if ((new Taskgroup($w))->canEdit($w->Auth->user())) {
            $w->msg('A taskgroup is required to create tasks', '/task-group/list');
        } else {
            $w->error('There are currently no taskgroups to add tasks to please contact an Administrator', '/task/list');
        }
    }

    $taskgroups = array_filter($taskgroup_list, function($taskgroup){
        return $taskgroup->getCanICreate();
    });

    $tasktypes = [];
    $priority = [];
    $members = [];

    // Try and prefetch the taskgroup by given id
    $taskgroup = null;
    $taskgroup_id = $w->request("gid");
    $assignee_id = 0;

    if (!empty($taskgroup_id) || !empty($task->task_group_id)) {
        $taskgroup = $w->Task->getTaskGroup(!empty($task->task_group_id) ? $task->task_group_id : $taskgroup_id);

        if (!empty($taskgroup->id)) {
            $tasktypes = $w->Task->getTaskTypes($taskgroup->task_group_type);
            $priority = $w->Task->getTaskPriority($taskgroup->task_group_type);
            $members = $w->Task->getMembersBeAssigned($taskgroup->id);
            sort($members);
            array_unshift($members,array("Unassigned","unassigned"));
            $assignee_id = (empty($task->assignee_id)) ? "unassigned" : $task->assignee_id;
        }
    }

    // Add history item
    History::add("Task: {$task->title}", null, $task);

    $status_list = Config::get("task." . $taskgroup->task_group_type)['statuses'];

    $w->ctx("t", (array)$task);
    $w->ctx("task", $task);
    $w->ctx("taskgroup_list", json_encode(array_map(function($task_group) {return ['value' => $task_group->id, 'text' => $task_group->title];}, $taskgroups)));
    $w->ctx("type_list", json_encode(array_map(function($tasktype) {return ['value' => $tasktype, 'text' => $tasktype];}, !empty($tasktypes[0]) ? $tasktypes[0] : [])));
    $w->ctx("status_list", json_encode(array_map(function($status) {return ['value' => $status[0], 'text' => $status[0]];}, $status_list)));
    $w->ctx("priority_list", json_encode(array_map(function($p) {return ['value' => $p[0], 'text' => $p[0]];}, $priority)));
    $w->ctx("assignee_list", json_encode(array_map(function($assignee) {return ['value' => $assignee[1], 'text' => $assignee[0]];}, $members)));
    $w->ctx("assignee_id", $assignee_id);
    $w->ctx("can_i_assign", $taskgroup->getCanIAssign());
    $w->ctx("title", 'Edit - ' . $task->title);
    //$w->ctx("canDelete", $task->canDelete($w->Auth->user()));
}
