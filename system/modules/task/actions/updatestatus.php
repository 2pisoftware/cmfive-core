<?php
// update status using dropdowns provided on Task List
function updatestatus_ALL(Web &$w) {
	// check for required REQUEST elements
	if ((Request::int('id')) && (Request::string('status'))) {
		// task is to get updated so gather relevant data
		$task = TaskService::getInstance($w)->getTask(Request::int('id'));

		// if task exists, first gather changes for display in comments
		if ($task) {
			$comments = "status updated to: " . Request::string('status') . "\n";

			$task->fill($_REQUEST);

			// if task has a 'closed' status, set flag so task no longer appear in dashboard count or task list
			if ($task->getisTaskClosed()) {
				$task->is_closed = 1;
				$task->dt_completed = date("d/m/Y");
			}
			else {
				$task->is_closed = 0;
			}
				
			$task->update();

			// we have comments, so add them
			$comm = new TaskComment($w);
			$comm->obj_table = $task->getDbTableName();
			$comm->obj_id = $task->id;
			$comm->comment = $comments;
			$comm->dt_created = Date("c");
			$comm->is_deleted = 0;
			$comm->insert();

			// add to context for notifications post listener
			$w->ctx("TaskComment",$comm);
			$w->ctx("TaskEvent","task_details");
		}
		// return
		$w->msg("Task: " . $task->title . " updated.","/task/tasklist/?assignee=".Request::string('assignee')."&creator=".Request::string('creator')."&taskgroups=".Request::string('taskgroups')."&tasktypes=".Request::string('tasktypes')."&tpriority=".Request::string('tpriority')."&status=".Request::string('tstatus')."&dt_from=".Request::string('dt_from')."&dt_to=".Request::string('dt_to'));
	}

	// return
	$w->msg("There was a problem.","/task/tasklist/");

}
