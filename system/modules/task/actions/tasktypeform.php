<?php
// Step II in creating a task. This function gets the additional fields by tasktype.
// Serialise REQUEST object from step one and store in hidden form element: 'formone'
function tasktypeform_POST(Web $w) {
	TaskService::getInstance($w)->navigation($w, "Create Task");

	// get task type, serialise REQUEST object from step 1 of creating a new task
	$tid = Request::string('task_type');
	$tg = TaskService::getInstance($w)->getTaskGroup(Request::int('task_group_id'));
	// if no due date given, make 1 month from today
	if (Request::string('dt_due') == "") {
		$_POST['dt_due'] = TaskService::getInstance($w)->getNextMonth();
	}

	$req = serialize($_POST);

	// get the additional form fields based on type type
	$theform = array();
	if ($tid != "") {
		$theform = TaskService::getInstance($w)->getFormFieldsByTask($tid,$tg);
	}

	if (!$theform) {
		$theform = array(array("Message","static","text","No further information required.<p>Please save your task."));
	}

	// combine input from step one with form fields for step II
	$hiden = array("","hidden","formone",$req);
	array_push($theform, $hiden);

	// display the form
	$f = Html::form($theform, $w->localUrl("/task/edit/"),"POST"," Submit ");
	$w->ctx("formfields",$f);
}
