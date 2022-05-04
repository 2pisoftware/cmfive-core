<?php
function updatetask_POST(Web &$w) {
	$p = $w->pathMatch("id");

	// task is to get updated so gather relevant data
	$task = TaskService::getInstance($w)->getTask($p['id']);
	$taskdata = TaskService::getInstance($w)->getTaskData($p['id']);

	// if task exists, first gather changes for display in comments
	if ($task) {
		// if no due date, make 1 month from now
		$dt_due = Request::string('dt_due');
		if (empty($dt_due)) { 
			$_POST['dt_due'] = TaskService::getInstance($w)->getNextMonth();
		}

		// convert dates to d/m/y for display. if assignee changes, get name of new assignee
		$comments = "";
		foreach ($_POST as $name => $value) {
			if (startsWith($name,"dt_")) {
				list($d,$m,$y) = preg_split('/\//',$value);
				$value = Date("U",strtotime($y . "-" . $m . "-" . $d));
			}
			if (($name != "FLOW_SID") && ($task->$name) && ($value != $task->$name)) {
				if (startsWith($name,"dt_"))
				$value = Date("d/m/Y",$value);
				if ($name == "assignee_id")
				$value = TaskService::getInstance($w)->getUserById($value);

				$comments .= $name . " updated to: " . $value . "\n";
			}
		}


		// update the task
		$task->fill($_POST);

		$task->update();

		// if we have comments, add them
		if (!empty($comments)) {
			$comm = new TaskComment($w);
			$comm->fill($_POST);
			$comm->obj_table = $task->getDbTableName();
			$comm->obj_id = $task->id;
			$comm->comment = $comments;
			$comm->insert();

			// add to context for notifications post listener
			$w->ctx("TaskComment",$comm);
			$w->ctx("TaskEvent","task_details");
		}
	}

	// if there is task data, update it also
	// if there is current no task data, but relevant input in the REQUEST object, create the task data
	if ($taskdata) {
		foreach ($taskdata as $td) {
			$arr = array("value"=>Request::string($td->key));
			$td->fill($arr);
			$td->update();
			unset($arr);
		}
	}
	else {
		foreach ($_POST as $name => $value) {
			// This is broken plus what is this?
			// if (($name != "FLOW_SID") && ($name != "task_id") && ($name !== CSRF::getTokenID())) {
			// 	$tdata = new TaskData($w);
			// 	$arr = array("task_id"=>$task->id,"key"=>$name,"value"=>$value);
			// 	$tdata->fill($arr);
			// 	$tdata->insert();
			// 	unset($arr);
			// }
		}
	}

	// return
	$w->msg("<div id='saved_record_id' data-id='".$task->id."' >Task: " . $task->title . " updated.</div>","/task/edit/".$task->id."?tab=1");
}
