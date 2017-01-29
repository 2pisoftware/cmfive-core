<?php
function viewtaskgrouptypes_ALL(Web $w) {
	$w->Task->navigation($w, "Manage Task Groups");
	
	History::add("Manage Task Groups");
	$task_groups = $w->Task->getTaskGroups();
	if ($task_groups) {
		usort($task_groups, array("TaskService","sortbyGroup"));
	}
	// prepare column headings for display
	$line = array(array("Title","Type", "Description", "Default Assignee"));

	// if task group exists, display title, group type, description, default assignee and button for specific task group info
	if ($task_groups) {
		foreach ($task_groups as $group) {
			$line[] = array(
					Html::a(WEBROOT."/task-group/viewmembergroup/".$group->id,$group->title),
					$group->getTypeTitle(),
					$group->description,
					$group->getDefaultAssigneeName(),
			);
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
	$arrassign = $w->Task->getTaskGroupPermissions();
	// unset 'ALL' given all can never assign a task
	unset($arrassign[0]);

	// set Is Task Active dropdown
	$is_active = array(array("Yes","1"), array("No","0"));

	$grouptypes = $w->Task->getAllTaskGroupTypes();
        $assignees = $w->Auth->getUsers();
        array_unshift($assignees,array("Unassigned","unassigned"));        

	// build form to create a new task group within the target group type
	$f = Html::form(array(
			array("Task Group Attributes","section"),
			array("Task Group Type","select","task_group_type",null,$grouptypes),
			array("Title","text","title"),
			array("Who Can Assign","select","can_assign",null,$arrassign),
			array("Who Can View","select","can_view",null,$w->Task->getTaskGroupPermissions()),
			array("Who Can Create","select","can_create",null,$w->Task->getTaskGroupPermissions()),
			array("Active","select","is_active",null,$is_active),
			array("","hidden","is_deleted","0"),
			array("Description","textarea","description",null,"26","6"),
			array("Default Task Type","select","default_task_type",null,null),
			array("Default Priority","select","default_priority",null,null),
			//array("Default Assignee","select","default_assignee_id",null,$assignees),
	),$w->localUrl("/task-group/createtaskgroup"),"POST","Save");

	// display form
	$w->ctx("creategroup",$f);
}
