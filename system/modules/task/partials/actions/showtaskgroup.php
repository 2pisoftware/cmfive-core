<?php namespace System\Modules\Task;

function showtaskgroup(\Web $w, $params) {
	$taskgroup = $params["taskgroup"];

	if (!empty($taskgroup)) {
		$taskgroup->tasks = TaskService::getInstance($w)->getTasksbyGroupId($taskgroup->id);
		$taskgroup->statuses = TaskService::getInstance($w)->getTaskTypeStatus($taskgroup->task_group_type);
		$w->ctx("taskgroup", $taskgroup);
	}

	$w->ctx("redirect", !empty($params["redirect"]) ? $params["redirect"] : "/");
}