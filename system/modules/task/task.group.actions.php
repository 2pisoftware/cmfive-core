<?php

////////////////////////////////////////////
//				TASK GROUPS				  //
////////////////////////////////////////////


// display an editable form showing attributes of a task group
function viewtaskgroup_GET(Web &$w) {
	$p = $w->pathMatch("id");
	// return task group details given a task group ID
	$group_details = $w->Task->getTaskGroup($p['id']);

	if (!empty($group_details)) {
		History::add("Taskgroup: ".$group_details->title, null, $group_details);
	}
	// if is_active is set to '0', display 'Yes', else display 'No'
	$isactive = $group_details->is_active == "1" ? "Yes" : "No";

	// set Is Task Active, Is Task Deleted dropdowns for display
	$is_active = array(array(__("Yes"),"1"), array(__("No"),"0"));
	$is_deleted = array(array(__("Yes"),"1"), array(__("No"),"0"));
	
	// get generic task group permissions
	$arrassign = $w->Task->getTaskGroupPermissions();
	// unset 'ALL' given all can never assign a task
	unset($arrassign[0]);

        // Get list of possible task types and priorities adn assignees
        $tasktypes = $w->Task->getTaskTypes($group_details->task_group_type);
        $priorities = $w->Task->getTaskPriority($group_details->task_group_type);
        $assignees = $w->Task->getMembersInGroup($p['id']);
        array_unshift($assignees,array(__("Unassigned"),"unassigned")); 
        // No default assignee means it is unassigned
        $default_assignee = (empty($group_details->default_assignee_id)) ? "unassigned" : $group_details->default_assignee_id;
	
	// build form displaying current attributes from database
	$f = Html::form(array(
	array(__("Task Group Details"),"section"),
	array(__("Task Group Type"),"static","task_group_type",$group_details->getTypeTitle()),
	array(__("Title"),"text", "title",$group_details->title),
	array(__("Who Can Assign"),"select", "can_assign",$group_details->can_assign,$arrassign),
	array(__("Who Can View"),"select", "can_view",$group_details->can_view,$w->Task->getTaskGroupPermissions()),
	array(__("Who Can Create"),"select","can_create",$group_details->can_create,$w->Task->getTaskGroupPermissions()),
	array(__("Is Active"),"select", "is_active", $group_details->is_active,$is_active),
	array(__("Description"),"textarea", "description",$group_details->description,"26","6"),
        array(__("Default Task Type","select","default_task_type",$group_details->default_task_type,$tasktypes),
        array(__("Default Priority"),"select","default_priority",$group_details->default_priority,$priorities),
	array(__("Default Assignee"),"select", "default_assignee_id",$default_assignee,$assignees),
	),$w->localUrl("/task-group/updatetaskgroup/".$group_details->id),"POST",__(" Update "));

	// display form
	$w->setLayout(null);
	$w->ctx("viewgroup",$f);
}

function createtaskgroup_POST(Web &$w) {
    $taskgroup = $w->Task->createTaskGroup(
        $w->request('task_group_type'),
        $w->request('title'),
        $w->request('description'),
        $w->request('default_assignee_id'),
        $w->request('can_assign'),
        $w->request('can_view'),
        $w->request('can_create'),
        $w->request('is_active'),
        $w->request('is_deleted'),
        $w->request('default_task_type'),
        $w->request('default_priority')
    );

    // return
    $w->msg("<div id='saved_record_id' data-id='".$taskgroup->id."' >".__("Task Group ").$taskgroup->title.__(" added")."</div>", "/task-group/viewmembergroup/".$taskgroup->id."#members");
}

function updatetaskgroup_POST(Web &$w) {
	$p = $w->pathMatch("id");
	// get details of task group being edited
	$group_details = $w->Task->getTaskGroup($p['id']);

	// if group exists, update the details
	if ($group_details) {
		$group_details->fill($_REQUEST);
		$response = $group_details->update();

                // Check the validation
                if ($response !== true) {
                    $w->errorMessage($group_details, "Taskgroup", $response, true, "/task-group/viewmembergroup/".$p['id']."#members");
                }                

		// if a default assignee is set (other than unassigned), return their membership object for this group
                $default_assignee_id = $_REQUEST['default_assignee_id'];
		if (!empty($default_assignee_id) && $default_assignee_id != "unassigned") {
			$mem = $w->Task->getMemberGroupById($group_details->id, $_REQUEST['default_assignee_id']);
		
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
		$w->msg(__("Task Group ") . $group_details->title . __(" updated."),"/task-group/viewmembergroup/".$group_details->id);
	}
	else {
		// if group somehow no longer exists, say as much
		$w->msg(__("Group: ") . $_REQUEST['title'] . __(" no longer exists?"),"/task-group/viewtaskgroups/".$group_details->task_group_type);
	}
}

function deletetaskgroup_GET(Web &$w) {
	$w->setLayout(null);
	
	$p = $w->pathMatch("id");
	
	// get details of task group to be deleted
	$taskgroup = $w->Task->getTaskGroup($p['id']);

	// if is_active is set to '0', display 'Yes', else display 'No'
	$isactive = $taskgroup->is_active == "1" ? "Yes" : "No";

	if (count($taskgroup->getTasks()) !== 0) {
		$w->out("<div class='row-fluid panel'>To be able to delete a task group, please ensure there are no active tasks</div>");
		return;
	}
	
	// build static form displaying group details for confirmation of delete
	$f = Html::form(array(
		array("Task Group Details","section"),
		array("Task Group Type","static", "task_group_type",$taskgroup->getTypeTitle()),
		array("Title","static", "title",$taskgroup->title),
		array("Who Can Assign","static", "can_assign",$taskgroup->can_assign),
		array("Who Can View","static", "can_view",$taskgroup->can_view),
		array("Who Can Create","static", "can_create",$taskgroup->can_create),
		array("Is Active","static", "is_active", $isactive),
		array("Description","static", "description",$taskgroup->description),
		array("Default Assignee","static", "default_assignee_id",$w->Task->getUserById($taskgroup->default_assignee_id)),
	), $w->localUrl("/task-group/deletetaskgroup/".$taskgroup->id),"POST","Delete");

	// display form
	$w->setLayout(null);
	$w->ctx("viewgroup",$f);
}

function deletetaskgroup_POST(Web &$w) {
    // Get path ID and check that it exists
    $p = $w->pathMatch("id");
    if (empty($p['id'])) {
        $w->error(__("Taskgroup could not be found"), "/task-group/viewtaskgrouptypes");
    }

    // Get taskgroup by given ID and make sure a taskgroup exists with that ID
    $taskgroup = $w->Task->getTaskGroup($p['id']);

    if (empty($taskgroup->id)) {
        $w->error(__("Taskgroup could not be found with ID: ") . $p['id'], "/task-group/viewtaskgrouptypes");
    }

    // Delete and return
    $taskgroup->delete();

    $w->msg(__("Task Group ") . $group_details->title . __(" deleted."),"/task-group/viewtaskgrouptypes");
}

////////////////////////////////////////////////////
//			MEMBER GROUPS						  //
////////////////////////////////////////////////////


function updategroupnotify_POST(Web &$w) {
	// lets get some values knowing that only checked checkboxes return a value
	$arr['guest']['creator'] = $_REQUEST['guest_creator'] ? $_REQUEST['guest_creator'] : "0"; 
	$arr['member']['creator'] = $_REQUEST['member_creator'] ? $_REQUEST['member_creator'] : "0"; 
	$arr['member']['assignee'] = $_REQUEST['member_assignee'] ? $_REQUEST['member_assignee'] : "0"; 
	$arr['owner']['creator'] = $_REQUEST['owner_creator'] ? $_REQUEST['owner_creator'] : "0"; 
	$arr['owner']['assignee'] = $_REQUEST['owner_assignee'] ? $_REQUEST['owner_assignee'] : "0"; 
	$arr['owner']['other'] = $_REQUEST['owner_other'] ? $_REQUEST['owner_other'] : "0"; 

	// so foreach role/type lets put the values in the database
	foreach ($arr as $role => $types) {
		foreach ($types as $type => $value) {
			// is there a record for this taskgroup > role > type?
			$notify = $w->Task->getTaskGroupNotifyType($_REQUEST['task_group_id'],$role,$type);
			
			// if yes, update, if no, insert
			if ($notify) {
				$notify->value = $value;
				$notify->update();
				}
			else {
				$notify = new TaskGroupNotify($w);
				$notify->task_group_id = $_REQUEST['task_group_id'];
				$notify->role = $role;
				$notify->type = $type;
				$notify->value = $value;
				$notify->insert();
			}
		}
	}
	
	// return
	$w->msg(__("Notifications Updated"),"/task-group/viewmembergroup/".$_REQUEST['task_group_id']."/?tab=2");
}

function viewmember_GET(Web &$w) {
	$p = $w->pathMatch("id");
	// get member details for edit
	$member = $w->Task->getMemberById($p['id']);

	// build editable form for a member allowing change of membership type
	$f = Html::form(array(
	array(__("Member Details"),"section"),
	array(__("Name"),"static", "name", $w->Task->getUserById($member->user_id)),
	array(__("Role"),"select","role",$member->role,$w->Task->getTaskGroupPermissions())
	),$w->localUrl("/task-group/updategroupmember/".$member->id),"POST",__(" Update "));

	// display form
    $w->setLayout(null);
	$w->ctx("viewmember",$f);
	}

function updategroupmember_POST(Web &$w) {
	$p = $w->pathMatch("id");
	$member = $w->Task->getMemberById($p['id']);
	$tgid = $member->task_group_id;

	$member->fill($_REQUEST);
	$member->update();

	$w->msg(__("Task Group updated"),"/task-group/viewmembergroup/".$tgid);
}

function addgroupmembers_GET(Web &$w) {
	$p = $w->pathMatch("task_group_id");

	// get all users (getUsers strips out users that are groups)
	$users = $w->Auth->getUsers();
	
	// build 'add members' form given task group ID, the list of group roles and the list of users.
	// if current members are added as if new, their membership will be updated, not recreated, with the selected role
	$addUserForm[__('Add Group Members')] = array(
		array(array("","hidden", "task_group_id", $p['task_group_id'])),
		array(array(__("As Role"), "select", "role", null, $w->Task->getTaskGroupPermissions())),
		array(array(__("Add Group Members"), "select", "member", null, $users))
	);

	$w->out(Html::multiColForm($addUserForm,$w->localUrl("/task-group/updategroupmembers/"),"POST",__("Submit")));
}

function updategroupmembers_POST(Web &$w) {
	// populate input array with preliminary membership details pertaining to target task group
	// these details will be the same for all new members to be added to the group
	$arrdb = array();
	$arrdb['task_group_id'] = $_REQUEST['task_group_id'];
	$arrdb['role'] = $_REQUEST['role'];
	$arrdb['priority'] = 1;
	$arrdb['is_active'] = 1;

	// for each selected member, complete population of input array
//	foreach ($_REQUEST['member'] as $member) {
		$arrdb['user_id'] = $w->request('member');
		// check to see if member already exists in this group
		$mem = $w->Task->getMemberGroupById($arrdb['task_group_id'], $arrdb['user_id']);
		
		// if no membership, create it
		if (!$mem) {
			$mem = new TaskGroupMember($w);
			$mem->fill($arrdb);
			$mem->insert();
			}
		else {
			// if membership does exists, update the record - only the role will be updated
			$mem->fill($arrdb);
			$mem->update();
		}
		// prepare input array for next selected member to insert/update
		unset($arrdb['user_id']);
//	}
	// return
	$w->msg(__("Task Group updated"),"/task-group/viewmembergroup/".$_REQUEST['task_group_id']);
}

function deletegroupmember_GET(Web &$w) {
	$p = $w->pathMatch("id");
	// get details of member to be deleted
	$member = $w->Task->getMemberById($p['id']);
	
	// build a static form displaying members details for confirmation of delete
	$f = Html::form(array(
	array(__("Member Details"),"section"),
	array("","hidden", "is_active","1"),
	array(__("Name"),"static", "name", $w->Task->getUserById($member->user_id)),
	array(__("Role"),"static","role",$member->role)
	),$w->localUrl("/task-group/deletegroupmember/".$member->id),"POST",__(" Delete "));

	// display form
    $w->setLayout(null);
	$w->ctx("deletegroupmember",$f);
	}

function deletegroupmember_POST(Web &$w) {
	$p = $w->pathMatch("id");
	// get the details of the person to delete 
	$member = $w->Task->getMemberById($p['id']);
	// get the task group ID for returning to group display
	$tgid = $member->task_group_id;

	// if member exists, delete them
	if (!empty($member)) {
		// set is_active = 1
		$member->delete();

		// get group details, if person being deleted is the task group default assignee
		// set default_assigne_id = 0, ie noone. owners can edit task group and assign default assignee at any time
		$group = $w->Task->getTaskGroup($tgid);
		if ($member->user_id == $group->default_assignee_id) {
			$group->default_assignee_id = 0;
			$group->update();
		}
		// return
		$w->msg(__("Task Group updated"),"/task-group/viewmembergroup/".$tgid);
	}
	else {
		// if member somehow no longer exists, say as much
		$w->msg(__("Task Group Members no longer exists?"),"/task-group/viewmembergroup/".$tgid);
	}
}
