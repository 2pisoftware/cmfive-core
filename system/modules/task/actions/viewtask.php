v<?php
function viewtask_GET(Web &$w) {
	$p = $w->pathMatch("id");

	// declare delete button
	$btndelete = "";

	// get relevant object for viewing a task given input task ID
	$task = $w->Task->getTask($p['id']);
	$w->ctx("task",$task);
	$taskdata = $w->Task->getTaskData($p['id']);
	$group = $w->Task->getTaskGroup($task->task_group_id);

	$w->Task->navigation($w, __("View Task: ") . $task->title);

	// if task is deleted, say as much and return to task list
	if ($task->is_deleted != 0) {
		$w->msg(__("This Task has been deleted"),"/task/tasklist/");
	}
	// check if i can view the task: my role in group Vs group can_view value
	elseif ($task->getCanIView()) {
		History::add(__("Task:")." {$task->title}");
		// tab: Task Details

		// if I can assign tasks, provide dropdown of group members else display current assignee.
		// my role in group Vs group can_assign value
		if ($task->getCanIAssign()) {
			$members = ($task) ? $w->Task->getMembersBeAssigned($task->task_group_id) : $w->Auth->getUsers();
			sort($members);
			$assign = array(__("Assigned To"),"select","assignee_id",$task->assignee_id,$members);
		}
		else {
			$assigned = ($task->assignee_id == "0") ? __("Not Assigned") : $w->Task->getUserById($task->assignee_id);
			$assign = array(__("Assigned To"),"static","assignee_id",$assigned);
		}

		//		changing type = dymanically loading of relevant form fields ... problem when presenting on single page.
		//		array("Task Type","select","task_type",$task->task_type,$task->getTaskGroupTypes()),

		// check a due date exists
		$dtdue = (($task->dt_due == "0000-00-00 00:00:00") || ($task->dt_due == "")) ? "" : date('d/m/Y',$task->dt_due);

		// Guests can view but not edit
		// See if i am assignee or creator, if yes provide editable form, else provide static display
		$btndelete = "";
		if ($task->getCanIEdit()) {
			$btndelete = Html::b(WEBROOT."/task/deletetask/".$task->id,__(" Delete Task "), __("Are you should you with to DELETE this task?"));

			// if task is closed and Task Group type says cannot be reopened, display static status
			if ($task->getisTaskClosed() && !$task->getTaskReopen()) {
				$status = array(__("Status"),"static","status",$task->status);
			}
			// otherwise, task is open, or is closed but can be reopened so allow edit of status
			else {
				$status = array(__("Status"),"select","status",$task->status,$task->getTaskGroupStatus());
			}
				
			$f = array(
			array(__("Task Details"),"section"),
			array(__("Title"),"text", "title", $task->title),
			array(__("Created By"),"static", "creator", $task->getTaskCreatorName()),
			array(__("Task Group"),"static","tg",$task->getTaskGroupTypeTitle()),
			array(__("Task Type"),"static","task_type",$task->getTypeTitle()),
			array(__("Description"),"static","tdesc",$task->getTypeDescription()),
			$status,
			array(__("Priority"),"select","priority",$task->priority,$task->getTaskGroupPriority()),
			array(__("Date Due"),"date","dt_due", $dtdue),
			array(__("Description"),"textarea", "description",$task->description,"80","15"),
			$assign,
			);
		}
		else {
			$f = array(
			array(__("Task Details"),"section"),
			array(__("Title"),"static", "title", $task->title),
			array(__("Created By"),"static", "creator", $task->getTaskCreatorName()),
			array(__("Task Group"),"static","tg",$task->getTaskGroupTypeTitle()),
			array(__("Task Type"),"static","task_type",$task->getTypeTitle()),
			array(__("Description"),"static","tdesc",$task->getTypeDescription()),
			array(__("Status"),"static","status",$task->status),
			array(__("Priority"),"static","priority",$task->priority),
			array(__("Date Due"),"static","dt_due", $dtdue),
			array(__("Description"),"static", "description",str_replace("\r\n","<br>",$task->description)),
			$assign,
			);
		}

		// got additional form fields for this task type
		$form = $w->Task->getFormFieldsByTask($task->task_type,$group);

		// if there are additional form fields, display them
		if ($form) {
			// string match form fields with task data by key
			// can then push db:value into form field for display
			foreach ($form as $x) {
				if ($x[1] == "section") {
					array_push($f, $x);
				}

				if ($taskdata) {
					foreach ($taskdata as $td) {
						$key = $td->key;
						$value = $td->value;

						// Guests can view but not edit
						// See if i am a guest, if yes provide static display, else provide editable form
						if (!$task->getCanIEdit())
						$x[1] = "static";
							
						if ($key == $x[2]) {
							$x[3] = $value;
							array_push($f, $x);
						}
					}
				}
				else {
					if ($x[1] != "section")
					array_push($f, $x);
				}
			}
		}

		// create form
		$form = Html::form($f,$w->localUrl("/task/updatetask/".$task->id),"POST",__(" Update "));

		// create 'start time log' button
		$buttontimelog = "";
		if ($task->assignee_id == $w->Auth->user()->id) {
                    $buttontimelog = new \Html\Button();
                    $buttontimelog->href("/task/starttimelog/{$task->id}")->setClass("startTime button small")->text(__("Start Time Log"));
                    // $btntimelog = "<button class=\"startTime\" href=\"/task/starttimelog/".$task->id."\"> Start Time Log </button>";
		} 

		// display variables
		$w->ctx("btntimelog",!empty($buttontimelog) ? $buttontimelog->__toString() : "");
		$w->ctx("btndelete",$btndelete);
		$w->ctx("viewtask",$form);
		$w->ctx("extradetails",$task->displayExtraDetails());

		
		// tab: time log
		// provide button to add time entry
		$addtime = "";
		if ($task->assignee_id == $w->Auth->user()->id) {		
                    $addtime = Html::box(WEBROOT."/task/addtime/".$task->id,__(" Add Time Log entry "),true);
		}
		$w->ctx("addtime",$addtime);

		// get time log for task
		$timelog = $task->getTimeLog();

		// set total period
		$totseconds = 0;

		// set headings
		$line = array(array(__("Assignee"), __("Start"), __("End"), __("Period (hours)"), __("Comment"), ""));
		// if log exists, display ...
		if ($timelog) {
			// for each entry display, calculate period and display total time on task
			foreach ($timelog as $log) {
				// get time difference, start to end
				$seconds = $log->dt_end - $log->dt_start;
				$period = $w->Task->getFormatPeriod($seconds);

				// if suspect, label button, style period, remove edit button
				if ($log->is_suspect == "1") {
					$label = __(" Accept ");
					$period = "(" . $period . ")";
					$bedit = "";
				}
				// if accepted, label button, tally period, include edit button
				if ($log->is_suspect == "0") {
					$label = __(" Review ");
					$totseconds += $seconds;
					$bedit = Html::box($w->localUrl("/task/addtime/".$task->id."/".$log->id),__(" Edit "),true);
				}

				// ony Task Group owner gets to reject/accept time log entries
				$bsuspect = ($w->Task->getIsOwner($task->task_group_id, $_SESSION['user_id'])) ? Html::b($w->localUrl("/task/suspecttime/".$task->id."/".$log->id),$label) : "";

				$line[] = array($w->Task->getUserById($log->user_id),
				$w->Task->getUserById($log->creator_id),
				formatDateTime($log->dt_start),
				formatDateTime($log->dt_end),
				$period,
				!empty($w->Comment->getComment($log->comment_id)) ? $w->Comment->getComment($log->comment_id)->comment:"",					
				$bedit .
								 
				Html::b($w->localUrl("/task/deletetime/".$task->id."/".$log->id),__(" Delete "),__("Are you sure you wish to DELETE this Time Log Entry?")) .
								
				$bsuspect .
								
				Html::box($w->localUrl("/task/popComment/".$task->id."/".$log->comment_id),__(" Comment "),true)
				);
			}
			$line[] = array("","","","<b>".__("Total")."</b>", "<b>".$w->Task->getFormatPeriod($totseconds)."</b>","");
		}
		else {
			$line[] = array(__("No time log entries have been made"),"","","","","");
		}

		// display the task time log
		$w->ctx("timelog",Html::table($line,null,"tablesorter",true));

		// tab: notifications
		// if i am assignee, creator or task group owner, i can get notifications for this task
		if ($task->getCanINotify()) {
				
			// get User set notifications for this Task
			$notify = $w->Task->getTaskUserNotify($_SESSION['user_id'],$task->id);
			if ($notify) {
				$task_creation = $notify->task_creation;
				$task_details = $notify->task_details;
				$task_comments = $notify->task_comments;
				$time_log = $notify->time_log;
				$task_documents = $notify->task_documents;
			}
			// no user notifications, get user set notifications for the Task Group
			else {
				// need my role in group
				$me = $w->Task->getMemberGroupById($task->task_group_id, $_SESSION['user_id']);
				// get task creator ID
				$creator_id = $task->getTaskCreatorId();

				// which am i?
				$assignee = ($task->assignee_id == $_SESSION['user_id']) ? true : false;
				$creator = ($creator_id == $_SESSION['user_id']) ? true : false;
				$owner = $w->Task->getIsOwner($task->task_group_id, $_SESSION['user_id']);

				// get single type given this is specific to a single Task
				if ($assignee) {
					$type = "assignee";
				}
				elseif ($creator) {
					$type = "creator";
				}
				elseif ($owner) {
					$type = "other";
				}

				$role = strtolower($me->role);

				if ($type) {
					// for type, check the User defined notification table
					$notify = $w->Task->getTaskGroupUserNotifyType($_SESSION['user_id'],$task->task_group_id,$role,$type);

					// get list of notification flags
					if ($notify) {
						if ($notify->value == "1") {
							$task_creation = $notify->task_creation;
							$task_details = $notify->task_details;
							$task_comments = $notify->task_comments;
							$time_log = $notify->time_log;
							$task_documents = $notify->task_documents;
							$task_pages = $notify->task_pages;
						}
					}
				}
			}
				
			// create form. if still no 'notify' all boxes are unchecked
			$f = array(array(__("For which Task Events should you receive Notification?"),"section"));
			$f[] = array("","hidden","task_creation", "0");
			$f[] = array(__("Task Details Update"),"checkbox","task_details", !empty($task_details) ? $task_details : null);
			$f[] = array(__("Comments Added"),"checkbox","task_comments", !empty($task_comments) ? $task_comments : null);
			$f[] = array(__("Time Log Entry"),"checkbox","time_log", !empty($time_log) ? $time_log : null);
			$f[] = array(__("Task Data Updated"),"checkbox","task_data", !empty($task_data) ? $task_data : null);
			$f[] = array(__("Documents Added"),"checkbox","task_documents", !empty($task_documents) ? $task_documents : null);

			$form = Html::form($f,$w->localUrl("/task/updateusertasknotify/".$task->id),"POST",__("Save"));


			// display
			$w->ctx("tasknotify",$form);
		}
	}
	else {
		// if i cannot view task details, return to task list with error message
		// for display get my role in the group, the group owners, the group title and the minimum membership required to view a task
		$me = $w->Task->getMemberGroupById($task->task_group_id, $_SESSION['user_id']);
		$myrole = (!$me) ? __("Not a Member") : $me->role;
		$owners = $w->Task->getTaskGroupOwners($task->task_group_id);

		// get owners names for display
                $strOwners = "";
		foreach ($owners as $owner) {
			$strOwners .= $w->Task->getUserById($owner->user_id) . ", ";
		}
		$strOwners = rtrim($strOwners,", ");

		$form = __("You must be at least a ")."<b>" . $group->can_view . "</b>".__(" of the Task Group").": <b>" . strtoupper($group->title) . "</b>, ".__("to view tasks in this group"_.".<p>".__("Your current Membership Level").": <b>" . $myrole . "</b><p>".__("Please appeal to the group owner(s)").": <b>" . $strOwners . "</b>".__(" for promotion.");

		$w->error($form,"/task/tasklist");
	}

}
