<?php
// Search Filter: selecting an Assignee will dynamically load the Group dropdown with available values
function taskAjaxAssigntoGroup_ALL(Web $w) {
	$group = array();
	$assignee = $w->request('id');

	// organise criteria
	$who = ($assignee != "") ? $assignee : null;
	$where = "is_closed = 0";

	// get task group titles from available task list
	$tasks = $w->Task->getTasks($who, $where);
	if ($tasks) {
		foreach ($tasks as $task) {
			if (!array_key_exists($task->task_group_id, $group))
			$group[$task->task_group_id] = array($task->getTaskGroupTypeTitle(),$task->task_group_id);
		}
	}
	if (!$group)
	$group = array(array("No assigned Tasks",""));

	// load Group dropdown and return
	$taskgroups = Html::select("taskgroups",$group,null);

	$w->setLayout(null);
	$w->out(json_encode($taskgroups));
}
