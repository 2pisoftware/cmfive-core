<?php

	$i = 0;
	$taskgroup = "";
	$assignee = $_SESSION['user_id'];
    $from = $w->Task->getLastWeek();
    $to = date("d/m/Y");
	
    // get all tasks in my groups answering criteria
    $tasks = $w->Task->getTaskWeek($taskgroup, $assignee, $from, $to);
	
    // set task activity heading
	$line = array(array(__("An overview of the activity in Tasks: ") . $from . __(" to ") . $to));
    if ($tasks) {
    	// dont wanna keep displaying same date so set a variable for comparison
        $olddate = "";
		foreach ($tasks as $task) {
			$taskgroup = $w->Task->getTaskGroup($task['task_group_id']);
			$caniview = $taskgroup->getCanIView();
			
			if ($caniview) {
				// if current task date = previous task date, dont display
				if (formatDate($task['dt_modified']) != $olddate) {
 					// if this is not the first record, display emtpy row between date lists
					if ($i > 0)
      				   $line[] = array("&nbsp;");
	      			// display fancy date
				   $line[] = array("<b>" . date("l jS F, Y", strtotime($task['dt_modified'])) . "</b>");
				}
				// display comments. if no group selected, display with link to task list with group preselected
	      		$thisgroup = ($taskgroup != "") ? "" : "<a title=\"".__("View Task Group")."\" href=\"" . $webroot . "/task/tasklist/?taskgroups=" . $task['task_group_id'] . "\">" . $w->Task->getTaskGroupTitleById($task['task_group_id']) . "</a>:&nbsp;&nbsp;";
				$line[] = array("<dd>" . date("g:i a", strtotime($task['dt_modified'])) . " - " . $thisgroup . "<a title=\"".__("View Task Details")."\" href=\"".$webroot."/task/viewtask/".$task['id']."\"><b>".$task['title']."</b></a>: " . $w->Task->findURL($task['comment']) . " - " . $w->Task->getUserById($task['creator_id']) . "</dd>");
				$olddate = formatDate($task['dt_modified']);
				$i++;
			}
		}
	}
	else {
		// if no tasks found, say as much
		$line[] = array(__("No Task Activity found for given date span"));
	}

	return Html::table($line,null,"tablesorter",true);

	?>
