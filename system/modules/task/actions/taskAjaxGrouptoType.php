<?php
// Search filter: selecting a Group will dynamically load the Type dropdown with available values
function taskAjaxGrouptoType_ALL(Web &$w) {
	$types = array();

	// split query string into group and assignee
	list($group, $assignee) = preg_split('/_/',Request::mixed('id'));

	// organise criteria
	$who = ($assignee != "") ? $assignee : null;
	$where = "";
	if ($group != "")
	$where .= "task_group_id = " . $group . " and ";

	$where .= "is_closed = 0";

	// get task types from available task list
	$tasks = TaskService::getInstance($w)->getTasks($who, $where);
	if ($tasks) {
		foreach ($tasks as $task) {
			if (!array_key_exists($task->task_type, $types))
			$types[$task->task_type] = array($task->getTypeTitle(),$task->task_type);
		}
	}
	if (!$types)
	$types = array(array("No assigned Tasks",""));

	// load type dropdown and return
	$tasktypes = Html::select("tasktypes",$types,null);

	$w->setLayout(null);
	$w->out(json_encode($tasktypes));
}
