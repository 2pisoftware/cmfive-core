<?php namespace System\Modules\Task;

function removeUser(\Web $w, $params = []) {
	$user = $params['user'];
	$redirect = $params['redirect'];
	
	// Get tasks that are assigned to this user
	$tasks = \TaskService::getInstance($w)->getObjects("Task", ["is_deleted" => 0, "assignee_id" => $user->id]);
	$task_group_membership = \TaskService::getInstance($w)->getObjects("TaskGroupMember", ["user_id" => $user->id]);
	
	$default_taskgroup_assignee = 0;
	$single_member_taskgroups = [];
	
	if (!empty($task_group_membership)) {
		foreach($task_group_membership as $membership) {
			$taskgroup = $membership->getTaskGroup();
			$task_group_member_count = $w->db->get("task_group_member")
										->where("is_active", 1)
										->where("task_group_id", $taskgroup->id)
										->count();
			
			// If there is only one member then add to warning array
			if ($task_group_member_count === 1) {
				$single_member_taskgroups[] = $taskgroup;
			}
			
			if ($taskgroup->default_assignee_id == $user->id) {
				$default_taskgroup_assignee++;
			}
		}
	}

	$w->ctx("user", $user);
	$w->ctx("default_taskgroup_assignee", $default_taskgroup_assignee);
	$w->ctx("tasks", $tasks);
	$w->ctx("task_group_membership", $task_group_membership);
	$w->ctx("single_member_taskgroups", $single_member_taskgroups);
	$w->ctx("redirect", $redirect);
}
