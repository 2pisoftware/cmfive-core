<?php

function unassign_POST(Web $w) {
	
	$user_id = $w->pathMatch();
	$redirect = Request::string("redirect");
	
	if (empty($user_id)) {
		$w->error("Missing User ID", $redirect ? : "/admin/users");
	}
	
	// Remove default assignee from taskgroup
	$taskgroups_default = TaskService::getInstance($w)->getObjects("TaskGroup", ["is_deleted" => 0, "default_assignee_id" => $user_id]);
	if (!empty($taskgroups_default)) {
		foreach($taskgroups_default as $taskgroup_assignee) {
			$taskgroup_assignee->default_assignee_id = null;
			$taskgroup_assignee->update(true, false);
		}
	}
	
	// Get taskgroup member ships
	$task_group_membership = TaskService::getInstance($w)->getObjects("TaskGroupMember", ["user_id" => $user_id]);
	
	if (!empty($task_group_membership)) {
		foreach($task_group_membership as $membership) {
			// Remove user from taskgroup
			$membership->delete();
		}
	}
	
	
	
	// Get tasks that are assigned to this user
	$tasks = TaskService::getInstance($w)->getObjects("Task", ["is_deleted" => 0, "assignee_id" => $user_id]);
	// Set assignee to default taskgroup assignee
	if (!empty($tasks)) {
		foreach($tasks as $task) {
			$taskgroup = $task->getTaskGroup();
			$task->assignee_id = $taskgroup->default_assignee_id;
			$task->update(true, false);
		}
	}
	
	$w->msg("User removed from Tasks/Taskgroups", $redirect ? : "/admin/users");
}