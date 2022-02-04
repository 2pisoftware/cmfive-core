<?php
function viewtaskgrouptypes_ALL(Web $w) {
	TaskService::getInstance($w)->navigation($w, "Manage Task Groups");
	
	

	History::add("Manage Task Groups");
	$task_groups = TaskService::getInstance($w)->getTaskGroups();
	if ($task_groups) {
		usort($task_groups, array("TaskService","sortbyGroup"));
	}
	// prepare column headings for display
	$headers = array("Title","Type", "Description", "Default Assignee");
	
	$line = array($headers);

	// if task group exists, display title, group type, description, default assignee and button for specific task group info
	if ($task_groups) {
		foreach ($task_groups as $group) {
			$row = array(
					Html::a(WEBROOT."/task-group/viewmembergroup/".$group->id,$group->title),
					$group->getTypeTitle(),
					$group->description,
					$group->getDefaultAssigneeName(),
			);
			
			$line[] = $row;
		}
	}
	else {
		// if no groups for this group type, say as much
		$line[] = array("There are no Task Groups Configured. Please create a New Task Group.","","","","");
	}

	// display list of task groups in the target task group type
	$w->ctx("dashboard",Html::table($line,null,"tablesorter",true));

	// tab: new task group
	// get generic task group permissions
	$arrassign = TaskService::getInstance($w)->getTaskGroupPermissions();
	// unset 'ALL' given all can never assign a task
	unset($arrassign[0]);

	

	$grouptypes = TaskService::getInstance($w)->getAllTaskGroupTypes();
        $assignees = AuthService::getInstance($w)->getUsers();
        array_unshift($assignees,array("Unassigned","unassigned"));        

	// build form to create a new task group within the target group type
	$f = Html::form(array(
			array("Task Group Attributes","section"),
			array("Task Group Type","select","task_group_type",null,$grouptypes),
			array("Title","text","title"),
			array("Who Can Assign","select","can_assign",null,$arrassign),
			array("Who Can View","select","can_view",null,TaskService::getInstance($w)->getTaskGroupPermissions()),
			array("Who Can Create","select","can_create",null,TaskService::getInstance($w)->getTaskGroupPermissions()),
			
			array("","hidden","is_deleted","0"),
			array("Description","textarea","description",null,"26","6"),
			array("Default Task Type","select","default_task_type",null,null),
			array("Default Priority","select","default_priority",null,null),
			array('Automatic Subscription', 'checkbox', 'is_automatic_subscription', TaskGroup::$_DEFAULT_AUTOMATIC_SUBSCRIPTION)
			//array("Default Assignee","select","default_assignee_id",null,$assignees),
	),$w->localUrl("/task-group/createtaskgroup"),"POST","Save");

	// display form
	$w->ctx("creategroup",$f);
}
