<?php
// Search Filter: selecting a Type will dynamically load the Priority dropdown with available values
function taskAjaxTypetoPriority_ALL(Web &$w) {
	$priority = array();

	// split the query string into type, group and assignee
	list($type, $group, $assignee) = preg_split('/_/',Request::mixed('id'));

	// organise criteria
	$who = ($assignee != "") ? $assignee : null;
	$where = "";
	if ($group != "")
	$where .= "task_group_id = " . $group . " and ";
	if ($type != "")
	$where .= "task_type = '" . $type . "' and ";

	$where .= "is_closed = 0";

	// get priorities from available task list
	$tasks = TaskService::getInstance($w)->getTasks($who, $where);
	if ($tasks) {
		foreach ($tasks as $task) {
			if (!array_key_exists($task->priority, $priority))
			$priority[$task->priority] = array($task->priority,$task->priority);
		}
	}
	if (!$priority)
	$priority = array(array("No assigned Tasks",""));

	// load priority dropdown and return
	$priority = HtmlBootstrap5::select("tpriority",$priority,null);

	$w->setLayout(null);
	$w->out(json_encode($priority));
}
