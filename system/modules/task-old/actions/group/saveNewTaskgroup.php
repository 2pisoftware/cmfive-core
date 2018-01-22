<?php

function saveNewTaskgroup_POST(Web $w) {
	
	// Get old and new taskgroup
	$old_taskgroup_id = $w->request("old_taskgroup_id");
	$new_taskgroup_id = $w->request("new_taskgroup_id");
	$task_id = $w->request("task_id");
	
	if (empty($old_taskgroup_id) || empty($new_taskgroup_id)) {
		$w->error("Missing taskgroup values", "/task/edit/" . $task_id);
	}
	
	if ($old_taskgroup_id == $new_taskgroup_id) {
		$w->error("No change to taskgroup", "/task/edit/" . $task_id);
	}
	
	$old_taskgroup = $w->Task->getTaskGroup($old_taskgroup_id);
	$new_taskgroup = $w->Task->getTaskGroup($new_taskgroup_id);
	$task = $w->Task->getTask($task_id);
	
	// If moving to taskgroup of same type
	$task->task_group_id = $new_taskgroup->id;
	if ($old_taskgroup->task_group_type !== $new_taskgroup->task_group_type) {
		$new_task_type = $w->request("new_task_type");
		$new_status = $w->request("new_task_status");
		$new_priority = $w->request("new_task_priority");
		$new_assignee = $w->request("new_task_assignee");
		
		$task->task_type = $new_task_type;
		$task->status = $new_status;
		$task->priority = $new_priority;
		$task->assignee_id = $new_assignee;
	}
	
	$task->update(false, false);
	$w->msg("Taskgroup changed", "/task/edit/" . $task->id);
		
}