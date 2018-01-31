<?php

function task_list_GET(Web $w) {
	
	$tasks = $w->Task->getTasks();

	$tasks_as_array = array_map(function($task) {
		$task_array = $task->toArray();
		$task_array['title'] = $task->toLink();
		$task_array['task_group_name'] = $task->getTaskGroup()->toLink();
		$task_array['assignee_name'] = $task->getAssignee()->getSelectOptionTitle();
		$task_array['dt_due'] = formatDate($task->dt_due);

		return $task_array;
	}, $tasks ? : []);

	$w->out((new JsonResponse())->setSuccessfulResponse('OK', $tasks_as_array));

}

function task_group_list_GET(Web $w) {
	
	$task_groups = $w->Task->getTaskGroups();

	$task_groups_as_array = array_map(function($task_group) {
		$task_group_array = $task_group->toArray();

		return $task_group_array;
	}, $task_groups ? : []);

	$w->out((new JsonResponse())->setSuccessfulResponse('OK', $task_groups_as_array));

}

function user_details_GET(Web $w) {

	list($user_id) = $w->pathMatch('user_id');

	$user = $w->Auth->getUser($user_id);
	if (empty($user->id) || !$user->canView($w->Auth->user())) {
		$w->out((new JsonResponse())->setMissingResponse('User not found'));
		return;
	}

	$data = [
		'name' => $user->getFullName(),
		'email' => $user->getContact()->email,
		'gravatar_hash' => md5(trim($user->getContact()->email))
	];

	$w->out((new JsonResponse())->setSuccessfulResponse('OK', $data));

}