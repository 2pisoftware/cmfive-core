<?php
// show task activity for the group and date span specified
function taskweek_ALL(Web &$w) {
	$w->Task->navigation($w, "");

	// if no group then no group
	$taskgroup = $w->request('taskgroup');
	// if no group member then no group member
	$assignee = $w->request('assignee');
	// if no from date then 7 days ago
	$from = $w->request('dt_from',$w->Task->getLastWeek());
	// if no to date then today
	$to = $w->request('dt_to',date("d/m/Y"));
	// display
	$w->ctx("from",$from);
	$w->ctx("to",$to);

	// get all tasks in my groups answering criteria
	$tasks = $w->Task->getTaskWeek($taskgroup, $assignee, $from, $to);

	// set task activity heading
	$line = array(array("An overview of the activity in Tasks: " . $from . " to " . $to));
	if ($tasks) {
		// dont wanna keep displaying same date so set a variable for comparison
		$olddate = "";
		$i = 0;
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
				$thisgroup = ($taskgroup != "") ? "" : "<a title=\"View Task Group\" href=\"" . WEBROOT . "/task/tasklist/?taskgroups=" . $task['task_group_id'] . "\">" . $w->Task->getTaskGroupTitleById($task['task_group_id']) . "</a>:&nbsp;&nbsp;";
				$line[] = array("<dd>" . date("g:i a", strtotime($task['dt_modified'])) . " - " . $thisgroup . "<a title=\"View Task Details\" href=\"".WEBROOT."/task/edit/".$task['id']."\"><b>".$task['title']."</b></a>: " . $w->Task->findURL($task['comment']) . " - " . $w->Task->getUserById($task['creator_id']) . "</dd>");
				$olddate = formatDate($task['dt_modified']);
				$i++;
			}
		}
	}
	else {
		// if no tasks found, say as much
		$line[] = array("No Task Activity found for given selections.");
	}

	// display
	$lines = Html::table($line,null,"tablesorter",true);
	$w->ctx("taskweek",$lines);

	// get list of groups of which i am a member
	$mygroups = $w->Task->getMemberGroups($_SESSION['user_id']);
	if ($mygroups) {
		foreach ($mygroups as $mygroup) {
			$taskgroup = $w->Task->getTaskGroup($mygroup->task_group_id);
			$caniview = $taskgroup->getCanIView();

			if ($caniview) {
				$group[$mygroup->task_group_id] = array($w->Task->getTaskGroupTitleById($mygroup->task_group_id),$mygroup->task_group_id);

				// for those groups of which i am a member, get list of all members for display in Assignee & Creator dropdowns
				$mymembers = $w->Task->getMembersInGroup($mygroup->task_group_id);
				foreach ($mymembers as $mymem) {
					$members[$mymem[1]] = array($mymem[0],$mymem[1]);
				}
			}
		}
		sort($members);
	}

	// load the search filters
	$a = Html::select("assignee",$members,$w->request('assignee'));
	$w->ctx("assignee",$a);

	$taskgroups = Html::select("taskgroup",$group, $w->request('taskgroup'));
	$w->ctx("taskgroups",$taskgroups);

}
