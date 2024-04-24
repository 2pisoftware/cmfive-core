<?php namespace System\Modules\Timelog;

function listtimelog(\Web $w, $params) {
	if (empty($params['object_class']) || empty($params['object_id'])) {
		return;
	}
	
	$timelogs = \TimelogService::getInstance($w)->getTimelogsForObjectByClassAndId($params['object_class'], $params['object_id']);
	if (!empty($timelogs)) {
		$total = array_reduce($timelogs, function($carry, $timelog) {
			return $carry += $timelog->getDuration();
		});
	}

	$all_tasks = \TaskService::getInstance($w)->getTasks();
	foreach ($all_tasks as $task) {
		if ($task->id == $params['object_id']) {
			$current_task = $task;
		}
	}
	if (in_array($current_task->task_type, ['Todo', 'CmfiveTicket', 'CrmTicket']) && sizeof($timelogs) > 0) {
		$task_time = \TaskTime::getInstance($w);
		$task_time->task_id = $params['object_id'];
	} else {
		$task_time = null;
	}
	
	$w->ctx("total", !empty($total) ? $total : 0);
	$w->ctx("class", $params['object_class']);
	$w->ctx("id", $params['object_id']);
	$w->ctx("redirect", !empty($params['redirect']) ? $params['redirect'] : "");
	$w->ctx("timelogs", $timelogs);
	$w->ctx("billable_hours", !empty($task_time) ? $task_time->getTotalTimeByBillable() : null);
}
