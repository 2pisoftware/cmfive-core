<?php
// Create Task: selecting Task Type dynamically loads the related task types, proprity and assignee's

use \Html\Form\Select as Select;

function taskAjaxSelectbyTaskGroup_ALL(Web $w)
{
    $p = $w->pathMatch("taskgroup_id");
    $task_group = TaskService::getInstance($w)->getTaskGroup($p['taskgroup_id']);

    if (empty($task_group->id)) {
        return;
    }

    $task_types = !empty($task_group) ? TaskService::getInstance($w)->getTaskTypes($task_group->task_group_type) : [];
    $priority = !empty($task_group) ? TaskService::getInstance($w)->getTaskPriority($task_group->task_group_type) : [];
    $members = !empty($task_group) ? TaskService::getInstance($w)->getMembersBeAssigned($task_group->id) : [];
    sort($members);
    $type_title = !empty($task_group) ? $task_group->getTypeTitle() : "";
    $type_desc = !empty($task_group) ? $task_group->getTypeDescription() : "";

    // if user cannot assign tasks in this group, leave 'first_assignee' blank for owner/member to delegate
    $members = ($task_group->getCanIAssign()) ? $members : [["Default", ""]];

    array_unshift($members, ["Unassigned", "unassigned"]);

    $result = [
        "types" => $task_types,
        "default_type" => $task_group->default_task_type,
        "priorities" => $priority,
        "default_priority" => $task_group->default_priority,
        "assignees" => $members,
        "default_assignee" => !empty($task_group->default_assignee_id) ? $task_group->default_assignee_id : null,
        "can_change_assignee" => !empty($task_group) && $task_group->getCanIAssign(),
        "statuses" => $task_group->getTypeStatus(),

        "group" => [
            "is_owner" => $task_group->isOwner(AuthService::getInstance($w)->user()),
			"name" => $task_group->title,
            "link" => $w->localUrl("task-group/viewmembergroup/" . $task_group->id),
            "type" => $type_title,
            "desc" => $type_desc,
        ]
    ];

    $w->setLayout(null);
    $w->out(json_encode($result));
}
