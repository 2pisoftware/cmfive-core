<?php

// display an editable form showing attributes of a task group
function viewtaskgroup_GET(Web &$w) {
	$p = $w->pathMatch("id");
	// return task group details given a task group ID
	$group_details = TaskService::getInstance($w)->getTaskGroup($p['id']);

	// if (!empty($group_details)) {
	// 	History::add("Taskgroup: ".$group_details->title, null, $group_details);
	// }
	// if is_active is set to '0', display 'Yes', else display 'No'
	$isactive = $group_details->is_active == "1" ? "Yes" : "No";

	// set Is Task Active, Is Task Deleted dropdowns for display
	$is_active = array(array("Yes","Yes"), array("No","No"));
	$is_deleted = array(array("Yes","1"), array("No","0"));
	
	// get generic task group permissions
	$arrassign = TaskService::getInstance($w)->getTaskGroupPermissions();
	// unset 'ALL' given all can never assign a task
	unset($arrassign[0]);

        // Get list of possible task types and priorities adn assignees
        $tasktypes = TaskService::getInstance($w)->getTaskTypes($group_details->task_group_type);
        $priorities = TaskService::getInstance($w)->getTaskPriority($group_details->task_group_type);
        $assignees = TaskService::getInstance($w)->getMembersInGroup($p['id']);
        array_unshift($assignees,array("Unassigned","unassigned")); 
        // No default assignee means it is unassigned
        $default_assignee = (empty($group_details->default_assignee_id)) ? "unassigned" : $group_details->default_assignee_id;
	
	// build form displaying current attributes from database
	$f = Html::form(array(
			array("Task Group Details", "section"),
			array("Task Group Type", "static", "task_group_type", $group_details->getTypeTitle()),
			array("Title", "text", "title", $group_details->title),
			array("Who Can Assign", "select", "can_assign", $group_details->can_assign, $arrassign),
			array("Who Can View", "select", "can_view", $group_details->can_view, TaskService::getInstance($w)->getTaskGroupPermissions()),
			array("Who Can Create", "select", "can_create", $group_details->can_create, TaskService::getInstance($w)->getTaskGroupPermissions()),
			//array("Is Active", "select", "is_active", $isactive, $is_active),
			array("Description", "textarea", "description", $group_details->description, "26", "6"),
			array("Default Task Type", "select", "default_task_type", $group_details->default_task_type, $tasktypes),
			array("Default Priority", "select", "default_priority", $group_details->default_priority, $priorities),
			array("Default Assignee", "select", "default_assignee_id", $default_assignee, $assignees),
			array('Automatic Subscription', 'checkbox', 'is_automatic_subscription', $group_details->is_automatic_subscription)
			), $w->localUrl("/task-group/viewtaskgroup/" . $group_details->id), "POST", "Update");

	// display form
	$w->setLayout(null);
	$w->ctx("viewgroup",$f);
}

function viewtaskgroup_POST(Web &$w) {
	$p = $w->pathMatch("id");
	// get details of task group being edited
	$group_details = TaskService::getInstance($w)->getTaskGroup($p['id']);

	// if group exists, update the details
	if ($group_details) {
        $group_details->fill($_REQUEST);
        //$group_details->is_active = $_POST['is_active'] == "Yes" ? 1 : 0;
		$group_details->is_automatic_subscription = !empty(Request::bool('is_automatic_subscription'));
		$response = $group_details->update();

                // Check the validation
                if ($response !== true) {
                    $w->errorMessage($group_details, "Taskgroup", $response, true, "/task-group/viewmembergroup/".$p['id']."#members");
                }                

		// if a default assignee is set (other than unassigned), return their membership object for this group
                $default_assignee_id = $_REQUEST['default_assignee_id'];
		if (!empty($default_assignee_id) && $default_assignee_id != "unassigned") {
			$mem = TaskService::getInstance($w)->getMemberGroupById($group_details->id, $_REQUEST['default_assignee_id']);
		
			// populate an array with the required details for updating
			// if the person is already a member we will maintain their current role
			// otherwise we will make them the group owner. we also make them active in case they had been previously removed from the group
			$arrdb = array();
			$arrdb['task_group_id'] = $group_details->id;
			$arrdb['user_id'] = $_REQUEST['default_assignee_id'];
			$arrdb['role'] = $mem->role ? $mem->role : "OWNER";
			$arrdb['priority'] = 1;
			$arrdb['is_active'] = 1;
			
			// if they don't exist, create the membership entry, otherwise update their current entry
			if (!$mem) {
				$mem = new TaskGroupMember($w);
				$mem->fill($arrdb);
				$mem->insert();
				}
			else {
				$mem->fill($arrdb);
				$mem->update();
				}
			}
		
		// return with message
		$w->msg("Task Group " . $group_details->title . " updated.","/task-group/viewmembergroup/".$group_details->id);
	}
	else {
		// if group somehow no longer exists, say as much
		$w->msg("Group: " . $_REQUEST['title'] . " no longer exists?","/task-group/viewtaskgroups/".$group_details->task_group_type);
	}
}





