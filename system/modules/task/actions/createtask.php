<?php
// Step I in creating a task. This function displays the default task creation form
function createtask_GET(Web &$w) {
	TaskService::getInstance($w)->navigation($w, "Create Task");

	// set default dropdowns for these task attributes as empty arrays
	// dropdowns are populated dynamically via JSON based upon task group type selected
	$tasktypes = array();
	$priority = array();
	$members = array();

	// get list of all task groups
	$taskgroups = TaskService::getInstance($w)->getTaskGroups();

	// whittle list of task groups down to only those in which i have role appropriate for creating tasks
	if ($taskgroups){
		foreach ($taskgroups as $tgroup) {
			if ($tgroup->getCanICreate()) {
				$mytaskgroups[] = array($tgroup->title,$tgroup->id);
			}
		}

		if (Request::mixed(!empty($key) ? $key : null) != "") {
			$t = TaskService::getInstance($w)->getTaskGroup(Request::int('gid'));

			$tasktypes = ($t != "") ? TaskService::getInstance($w)->getTaskTypes($t->task_group_type) : array();
			$priority = ($t != "") ? TaskService::getInstance($w)->getTaskPriority($t->task_group_type) : array();
			$members = ($t != "") ? TaskService::getInstance($w)->getMembersBeAssigned($t->id) : array();
			sort($members);
				
			$tasktext = "<table>" .
				"<tr><td class=section colspan=2>Task Group Description</td></tr>" . 
				"<tr><td><b>Task Group</td><td>" . $t->title . "</td></tr>" . 
				"<tr><td><b>Task Type</b></td><td>" . $t->getTypeTitle() . "</td></tr>" . 
				"<tr><td><b>Description</b></td><td>" . $t->getTypeDescription() . "</td></tr>" . 
				"</table>";

			$w->ctx("tasktext",$tasktext);
		}

		// build form
		$f = HtmlBootstrap5::form(array(
		array("Create a New Task - Step One","section"),
		array("Task Group","select","task_group_id",Request::int('gid'),$mytaskgroups),
		array("Task Title","text","title"),
		array("Task Type","select","task_type",null,$tasktypes),
		array("Priority","select","priority",null,$priority),
		array("Date Due","date","dt_due"),
		array("Description","textarea","description",null,110,15),
		array("Assigned To","select","first_assignee_id",null,$members),
		),$w->localUrl("/task/tasktypeform/"),"POST"," Continue >> ");

                $w->ctx("createtask",$f);
	}
}

function createtask_POST(Web &$w) {
	TaskService::getInstance($w)->navigation($w, "Create Task");

	// unserialise input from step I and store in array: arr_req
	$arr_req = unserialize(Request::mixed('formone'));

	// insert Task into database
	$task = new Task($w);
	$task->fill($arr_req);

	$task->insert();

	// if insert is successful, store additional fields as task data
	// we do not want to store data from step I, the task_id (as a key=>value pair) nor the FLOW_SID
	if ($task->id) {
		foreach ($_POST as $name => $value) {
			if (($name != "formone") && ($name != "FLOW_SID") && ($name != "task_id") && ($name !== CSRF::getTokenID())) {
				$tdata = new TaskData($w);
				$arr = array("task_id"=>$task->id,"key"=>$name,"value"=>$value);
				$tdata->fill($arr);
				$tdata->insert();
				unset($arr);
			}
		}

		// return to task dashboard
		$w->msg("<div id='saved_record_id' data-id='".$task->id."' >Task ".$task->title." added</div>","/task/viewtask/".$task->id);
	}
	else {
		// if task insert was unsuccessful, say as much
		$w->msg("The Task could not be created. Please inform the IT Group","/task/index/");
	}
}
