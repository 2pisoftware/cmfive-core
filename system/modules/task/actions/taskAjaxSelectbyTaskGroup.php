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

    $ttype = "Task Type <small>Required</small>" . (new Select())
        ->setName("task_type")
        ->setId("task_type")
        ->setOptions($task_types)
        ->setSelectedOption($task_group->default_task_type)
        ->setRequired('required')->__toString();

    $prior = Html::select("priority", $priority, $task_group->default_priority);
    array_unshift($members, ["Unassigned", "unassigned"]);

    $assigned_to = (new \Html\Form\Select([
        "id|name" => "assignee_id",
        "required" => true,
        "disabled" => !empty($task_group) && $task_group->getCanIAssign() ? null : "disabled",
    ]))->setOptions($members, true);

    if (!empty($task_group->default_assignee_id)) {
        $assigned_to->setSelectedOption($task_group->default_assignee_id);
    }

    $mem = "<label>Assigned To" . $assigned_to . "</label>";

    $taskgroup_url = $w->localUrl("task-group/viewmembergroup/" . $task_group->id);
    $show_link = $task_group->isOwner(AuthService::getInstance($w)->user()) && 
                AuthService::getInstance($this)->allowed($taskgroup_url);
    $taskgroup_link = $show_link ?
     "<a href=\"" . $taskgroup_url . "\">" . $task_group->title . "</a>" :
      $task_group->title;

    $task_text = "<table style='width: 100%;'>" .
        "<tr><td class=section colspan=2>Task Group Description</td></tr>" .
        "<tr><td><b>Task Group</td><td>" . $taskgroup_link . "</td></tr>" .
        "<tr><td><b>Task Type</b></td><td>" . $type_title . "</td></tr>" .
        "<tr valign=top><td><b>Description</b></td><td>" . $type_desc . "</td></tr>" .
        "</table>";

    // return as array of arrays
    $result = [$ttype, $prior, $mem, $task_text, Html::select("status", $task_group->getTypeStatus(), null, null, null, null)];

    $w->setLayout(null);
    $w->out(json_encode($result));
}
