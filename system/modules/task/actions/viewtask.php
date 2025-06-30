<?php
function viewtask_GET(Web &$w) {
	$p = $w->pathMatch("id");

	// declare delete button
	$btndelete = "";

	// get relevant object for viewing a task given input task ID
	$task = TaskService::getInstance($w)->getTask($p['id']);
	$w->ctx("task",$task);
	$taskdata = TaskService::getInstance($w)->getTaskData($p['id']);
	$group = TaskService::getInstance($w)->getTaskGroup($task->task_group_id);

	TaskService::getInstance($w)->navigation($w, "View Task: " . $task->title);

	// if task is deleted, say as much and return to task list
	if ($task->is_deleted != 0) {
		$w->msg("Task not found","/task/tasklist/");
	}
	// check if i can view the task: my role in group Vs group can_view value
	elseif ($task->getCanIView()) {
		History::add("Task: {$task->title}");
		// tab: Task Details

		// if I can assign tasks, provide dropdown of group members else display current assignee.
		// my role in group Vs group can_assign value
		if ($task->getCanIAssign()) {
			$members = ($task) ? TaskService::getInstance($w)->getMembersBeAssigned($task->task_group_id) : AuthService::getInstance($w)->getUsers();
			sort($members);
			$assign = array("Assigned To","select","assignee_id",$task->assignee_id,$members);
		}
		else {
			$assigned = ($task->assignee_id == "0") ? "Not Assigned" : TaskService::getInstance($w)->getUserById($task->assignee_id);
			$assign = array("Assigned To","static","assignee_id",$assigned);
		}

		//		changing type = dymanically loading of relevant form fields ... problem when presenting on single page.
		//		array("Task Type","select","task_type",$task->task_type,$task->getTaskGroupTypes()),

		// check a due date exists
		$dtdue = (($task->dt_due == "0000-00-00 00:00:00") || ($task->dt_due == "")) ? "" : date('d/m/Y',$task->dt_due);

		// Guests can view but not edit
		// See if i am assignee or creator, if yes provide editable form, else provide static display
		$btndelete = "";
		if ($task->getCanIEdit()) {
			$btndelete = HtmlBootstrap5::b(WEBROOT."/task/deletetask/".$task->id," Delete Task ", "Are you should you with to DELETE this task?");

			// if task is closed and Task Group type says cannot be reopened, display static status
			if ($task->getisTaskClosed() && !$task->getTaskReopen()) {
				$status = array("Status","static","status",$task->status);
			}
			// otherwise, task is open, or is closed but can be reopened so allow edit of status
			else {
				$status = array("Status","select","status",$task->status,$task->getTaskGroupStatus());
			}
				
			$f = array(
			array("Task Details","section"),
			array("Title","text", "title", $task->title),
			array("Created By","static", "creator", $task->getTaskCreatorName()),
			array("Task Group","static","tg",$task->getTaskGroupTypeTitle()),
			array("Task Type","static","task_type",$task->getTypeTitle()),
			array("Description","static","tdesc",$task->getTypeDescription()),
			$status,
			array("Priority","select","priority",$task->priority,$task->getTaskGroupPriority()),
			array("Date Due","date","dt_due", $dtdue),
			array("Description","textarea", "description",$task->description,"80","15"),
			$assign,
			);
		}
		else {
			$f = array(
			array("Task Details","section"),
			array("Title","static", "title", $task->title),
			array("Created By","static", "creator", $task->getTaskCreatorName()),
			array("Task Group","static","tg",$task->getTaskGroupTypeTitle()),
			array("Task Type","static","task_type",$task->getTypeTitle()),
			array("Description","static","tdesc",$task->getTypeDescription()),
			array("Status","static","status",$task->status),
			array("Priority","static","priority",$task->priority),
			array("Date Due","static","dt_due", $dtdue),
			array("Description","static", "description",str_replace("\r\n","<br>",$task->description)),
			$assign,
			);
		}

		// got additional form fields for this task type
		$form = TaskService::getInstance($w)->getFormFieldsByTask($task->task_type,$group);

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
		$form = HtmlBootstrap5::form($f,$w->localUrl("/task/updatetask/".$task->id),"POST"," Update ");

		// create 'start time log' button
		$buttontimelog = "";
		if ($task->assignee_id == AuthService::getInstance($w)->user()->id) {
                    $buttontimelog = new \Html\Button();
                    $buttontimelog->href("/task/starttimelog/{$task->id}")->setClass("startTime button small")->text("Start Time Log");
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
		if ($task->assignee_id == AuthService::getInstance($w)->user()->id) {		
                    $addtime = HtmlBootstrap5::box(WEBROOT."/task/addtime/".$task->id," Add Time Log entry ",true);
		}
		$w->ctx("addtime",$addtime);

		// get time log for task
		$timelog = $task->getTimeLog();

		// set total period
		$totseconds = 0;

		// set headings
		$line = array(array("Assignee", "Start", "End", "Period (hours)", "Comment", ""));
		// if log exists, display ...
		if ($timelog) {
			// for each entry display, calculate period and display total time on task
			foreach ($timelog as $log) {
				// get time difference, start to end
				$seconds = $log->dt_end - $log->dt_start;
				$period = TaskService::getInstance($w)->getFormatPeriod($seconds);

				// if suspect, label button, style period, remove edit button
				if ($log->is_suspect == "1") {
					$label = " Accept ";
					$period = "(" . $period . ")";
					$bedit = "";
				}
				// if accepted, label button, tally period, include edit button
				if ($log->is_suspect == "0") {
					$label = " Review ";
					$totseconds += $seconds;
					$bedit = HtmlBootstrap5::box($w->localUrl("/task/addtime/".$task->id."/".$log->id)," Edit ",true);
				}

				// ony Task Group owner gets to reject/accept time log entries
				$bsuspect = (TaskService::getInstance($w)->getIsOwner($task->task_group_id, $_SESSION['user_id'])) ? HtmlBootstrap5::b($w->localUrl("/task/suspecttime/".$task->id."/".$log->id),$label) : "";

				$line[] = array(TaskService::getInstance($w)->getUserById($log->user_id),
				TaskService::getInstance($w)->getUserById($log->creator_id),
				formatDateTime($log->dt_start),
				formatDateTime($log->dt_end),
				$period,
				!empty(CommentService::getInstance($w)->getComment($log->comment_id)) ? CommentService::getInstance($w)->getComment($log->comment_id)->comment:"",					
				$bedit .
								 
				HtmlBootstrap5::b($w->localUrl("/task/deletetime/".$task->id."/".$log->id)," Delete ","Are you sure you wish to DELETE this Time Log Entry?") .
								
				$bsuspect .
								
				HtmlBootstrap5::box($w->localUrl("/task/popComment/".$task->id."/".$log->comment_id)," Comment ",true)
				);
			}
			$line[] = array("","","","<b>Total</b>", "<b>".TaskService::getInstance($w)->getFormatPeriod($totseconds)."</b>","");
		}
		else {
			$line[] = array("No time log entries have been made","","","","","");
		}

		// display the task time log
		$w->ctx("timelog",HtmlBootstrap5::table($line,null,"tablesorter",true));

		// tab: notifications
		// if i am assignee, creator or task group owner, i can get notifications for this task
		if ($task->getCanINotify()) {
				
			// get User set notifications for this Task
			$notify = TaskService::getInstance($w)->getTaskUserNotify($_SESSION['user_id'],$task->id);
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
				$me = TaskService::getInstance($w)->getMemberGroupById($task->task_group_id, $_SESSION['user_id']);
				// get task creator ID
				$creator_id = $task->getTaskCreatorId();

				// which am i?
				$assignee = ($task->assignee_id == $_SESSION['user_id']) ? true : false;
				$creator = ($creator_id == $_SESSION['user_id']) ? true : false;
				$owner = TaskService::getInstance($w)->getIsOwner($task->task_group_id, $_SESSION['user_id']);

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
					$notify = TaskService::getInstance($w)->getTaskGroupUserNotifyType($_SESSION['user_id'],$task->task_group_id,$role,$type);

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
			$f = array(array("For which Task Events should you receive Notification?","section"));
			$f[] = array("","hidden","task_creation", "0");
			$f[] = array("Task Details Update","checkbox","task_details", !empty($task_details) ? $task_details : null);
			$f[] = array("Comments Added","checkbox","task_comments", !empty($task_comments) ? $task_comments : null);
			$f[] = array("Time Log Entry","checkbox","time_log", !empty($time_log) ? $time_log : null);
			$f[] = array("Task Data Updated","checkbox","task_data", !empty($task_data) ? $task_data : null);
			$f[] = array("Documents Added","checkbox","task_documents", !empty($task_documents) ? $task_documents : null);

			$form = HtmlBootstrap5::form($f,$w->localUrl("/task/updateusertasknotify/".$task->id),"POST","Save");


			// display
			$w->ctx("tasknotify",$form);
		}
	}
	else {
		// if i cannot view task details, return to task list with error message
		// for display get my role in the group, the group owners, the group title and the minimum membership required to view a task
		$me = TaskService::getInstance($w)->getMemberGroupById($task->task_group_id, $_SESSION['user_id']);
		$myrole = (!$me) ? "Not a Member" : $me->role;
		$owners = TaskService::getInstance($w)->getTaskGroupOwners($task->task_group_id);

		// get owners names for display
                $strOwners = "";
		foreach ($owners as $owner) {
			$strOwners .= TaskService::getInstance($w)->getUserById($owner->user_id) . ", ";
		}
		$strOwners = rtrim($strOwners,", ");

		$form = "You must be at least a <b>" . $group->can_view . "</b> of the Task Group: <b>" . strtoupper($group->title) . "</b>, to view tasks in this group.<p>Your current Membership Level: <b>" . $myrole . "</b><p>Please appeal to the group owner(s): <b>" . $strOwners . "</b> for promotion.";

		$w->error($form,"/task/tasklist");
	}

}
