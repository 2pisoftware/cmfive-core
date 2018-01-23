<?php

function task_list_GET(Web $w) {
	
	$tasks = $w->Task->getTasks();

	$task_array = array_map(function($task) {
		$task_array = $task->toArray();
		$task_array['task_group_name'] = $task->getTaskGroup()->getSelectOptionTitle();
		$task_array['assignee_name'] = $task->getAssignee()->getSelectOptionTitle();
		$task_array['dt_due'] = formatDate($task->dt_due);

		return $task_array;
	}, $tasks ? : []);

	$w->out((new JsonResponse())->setSuccessfulResponse('OK', $task_array));

}
