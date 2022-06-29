<?php
// Search Filter: selecting a Priority will dynamically load the Status dropdown with available values
function taskAjaxPrioritytoStatus_ALL(Web &$w)
{
	$status = array();

	// split query string into proirity, type, group and assignee
	list($priority, $type, $group, $assignee) = preg_split('/_/', Request::mixed('id'));

	// organise criteria
	$who = ($assignee != "") ? $assignee : null;
	$where = "";
	if ($group != "")
		$where .= "task_group_id = " . $group . " and ";
	if ($type != "")
		$where .= "task_type = '" . $type . "' and ";
	if ($priority != "")
		$where .= "priority = '" . $priority . "' and ";

	$where .= "is_closed = 0";

	// get statuses from available tasks
	$tasks = TaskService::getInstance($w)->getTasks($who, $where);
	if ($tasks) {
		foreach ($tasks as $task) {
			if (!array_key_exists($task->status, $status))
				$status[$task->status] = array($task->status, $task->status);
		}
	}
	if (!$status)
		$status = array(array("No assigned Tasks", ""));

	// load status dropdown and return
	$status = Html::select("status", $status, null);

	$w->setLayout(null);
	$w->out(json_encode($status));
}
