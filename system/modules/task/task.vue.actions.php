<?php



function task_list_GET(Web $w) {
	
	$tasks = $w->Task->getTasks();



	$task_array = array_map(function($task) {
		return $task->toArray();
	}, $tasks ? : []);

	$w->out((new JsonResponse())->setSuccessfulResponse('OK', $task_array));

}