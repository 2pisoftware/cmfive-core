<?php

////////////////////////////////////////////
//				TASK GROUPS				  //
////////////////////////////////////////////

function createtaskgroup_POST(Web &$w)
{
	$taskgroup = TaskService::getInstance($w)->createTaskGroup(
		Request::string('task_group_type'),
		Request::string('title'),
		Request::string('description'),
		Request::int('default_assignee_id'),
		Request::string('can_assign'),
		Request::string('can_view'),
		Request::string('can_create'),
		1,
		Request::string('is_deleted'),
		Request::string('default_task_type'),
		Request::string('default_priority'),
		Request::bool('is_automatic_subscription')
	);

	// return
	$w->msg("<div id='saved_record_id' data-id='" . $taskgroup->id . "' >Task Group " . $taskgroup->title . " added</div>", "/task-group/viewmembergroup/" . $taskgroup->id . "#members");
}



function deletetaskgroup_GET(Web &$w)
{
	$w->setLayout(null);

	$p = $w->pathMatch("id");

	// get details of task group to be deleted
	$taskgroup = TaskService::getInstance($w)->getTaskGroup($p['id']);

	// if is_active is set to '0', display 'Yes', else display 'No'
	$isactive = $taskgroup->is_active == "1" ? "Yes" : "No";

	if (count($taskgroup->getUnclosedTasks()) !== 0) {
		$w->out("<div class='row-fluid panel'>To be able to delete a task group, please ensure there are no active tasks</div>");
		return;
	}

	// build static form displaying group details for confirmation of delete
	$f = Html::form(array(
		array("Task Group Details", "section"),
		array("Task Group Type", "static", "task_group_type", $taskgroup->getTypeTitle()),
		array("Title", "static", "title", $taskgroup->title),
		array("Who Can Assign", "static", "can_assign", $taskgroup->can_assign),
		array("Who Can View", "static", "can_view", $taskgroup->can_view),
		array("Who Can Create", "static", "can_create", $taskgroup->can_create),
		array("Is Active", "static", "is_active", $isactive),
		array("Description", "static", "description", $taskgroup->description),
		array("Default Assignee", "static", "default_assignee_id", TaskService::getInstance($w)->getUserById($taskgroup->default_assignee_id)),
	), $w->localUrl("/task-group/deletetaskgroup/" . $taskgroup->id), "POST", "Delete");

	// display form

	$w->ctx("viewgroup", $f);
}

function deletetaskgroup_POST(Web &$w)
{
	// Get path ID and check that it exists
	$p = $w->pathMatch("id");
	if (empty($p['id'])) {
		$w->error("Taskgroup could not be found", "/task-group/viewtaskgrouptypes");
	}

	// Get taskgroup by given ID and make sure a taskgroup exists with that ID
	$taskgroup = TaskService::getInstance($w)->getTaskGroup($p['id']);

	if (empty($taskgroup->id)) {
		$w->error("Taskgroup could not be found with ID: " . $p['id'], "/task-group/viewtaskgrouptypes");
	}

	// Delete and return
	$taskgroup->delete();

	$w->msg("Task Group " . $taskgroup->title . " deleted.", "/task-group/viewtaskgrouptypes");
}

////////////////////////////////////////////////////
//			MEMBER GROUPS						  //
////////////////////////////////////////////////////


function updategroupnotify_POST(Web &$w)
{
	// lets get some values knowing that only checked checkboxes return a value
	$arr['guest']['creator'] = $_REQUEST['guest_creator'] ? $_REQUEST['guest_creator'] : "0";
	$arr['member']['creator'] = $_REQUEST['member_creator'] ? $_REQUEST['member_creator'] : "0";
	$arr['member']['assignee'] = $_REQUEST['member_assignee'] ? $_REQUEST['member_assignee'] : "0";
	$arr['member']['assignee'] = $_REQUEST['member_other'] ? $_REQUEST['member_other'] : "0";
	$arr['owner']['creator'] = $_REQUEST['owner_creator'] ? $_REQUEST['owner_creator'] : "0";
	$arr['owner']['assignee'] = $_REQUEST['owner_assignee'] ? $_REQUEST['owner_assignee'] : "0";
	$arr['owner']['other'] = $_REQUEST['owner_other'] ? $_REQUEST['owner_other'] : "0";

	// so foreach role/type lets put the values in the database
	foreach ($arr as $role => $types) {
		foreach ($types as $type => $value) {
			// is there a record for this taskgroup > role > type?
			$notify = TaskService::getInstance($w)->getTaskGroupNotifyType($_REQUEST['task_group_id'], $role, $type);

			// if yes, update, if no, insert
			if ($notify) {
				$notify->value = $value;
				$notify->update();
			} else {
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
	$w->msg("Notifications Updated", "/task-group/viewmembergroup/" . $_REQUEST['task_group_id'] . "/?tab=2");
}

function viewmember_GET(Web &$w)
{
	$p = $w->pathMatch("id");
	// get member details for edit
	$member = TaskService::getInstance($w)->getMemberById($p['id']);

	// build editable form for a member allowing change of membership type
	$f = Html::form(array(
		array("Member Details", "section"),
		array("Name", "static", "name", TaskService::getInstance($w)->getUserById($member->user_id)),
		array("Role", "select", "role", $member->role, TaskService::getInstance($w)->getTaskGroupPermissions())
	), $w->localUrl("/task-group/updategroupmember/" . $member->id), "POST", " Update ");

	// display form
	$w->setLayout(null);
	$w->ctx("viewmember", $f);
}

function updategroupmember_POST(Web &$w)
{
	$p = $w->pathMatch("id");
	$member = TaskService::getInstance($w)->getMemberById($p['id']);
	$tgid = $member->task_group_id;

	$member->fill($_REQUEST);
	$member->update();

	$w->msg("Task Group updated", "/task-group/viewmembergroup/" . $tgid);
}

function addgroupmembers_GET(Web &$w)
{
	$p = $w->pathMatch("task_group_id");

	// get all users (getUsers strips out users that are groups)
	$users = AuthService::getInstance($w)->getUsers();

	// build 'add members' form given task group ID, the list of group roles and the list of users.
	// if current members are added as if new, their membership will be updated, not recreated, with the selected role
	$addUserForm['Add Group Members'] = array(
		array(array("", "hidden", "task_group_id", $p['task_group_id'])),
		array(array("As Role", "select", "role", null, TaskService::getInstance($w)->getTaskGroupPermissions())),
		array(array("Add Group Members", "select", "member", null, $users))
	);

	$w->out(Html::multiColForm($addUserForm, $w->localUrl("/task-group/updategroupmembers/"), "POST", "Submit"));
}

function updategroupmembers_POST(Web &$w)
{
	// populate input array with preliminary membership details pertaining to target task group
	// these details will be the same for all new members to be added to the group
	$arrdb = array();
	$arrdb['task_group_id'] = $_REQUEST['task_group_id'];
	$arrdb['role'] = $_REQUEST['role'];
	$arrdb['priority'] = 1;
	$arrdb['is_active'] = 1;

	// for each selected member, complete population of input array
	//	foreach ($_REQUEST['member'] as $member) {
	$arrdb['user_id'] = Request::string('member');
	// check to see if member already exists in this group
	$mem = TaskService::getInstance($w)->getMemberGroupById($arrdb['task_group_id'], $arrdb['user_id']);

	// if no membership, create it
	if (!$mem) {
		$mem = new TaskGroupMember($w);
		$mem->fill($arrdb);
		$mem->insert();
	} else {
		// if membership does exists, update the record - only the role will be updated
		$mem->fill($arrdb);
		$mem->update();
	}
	// prepare input array for next selected member to insert/update
	unset($arrdb['user_id']);
	//	}
	// return
	$w->msg("Task Group updated", "/task-group/viewmembergroup/" . $_REQUEST['task_group_id']);
}

function deletegroupmember_GET(Web &$w)
{
	$p = $w->pathMatch("id");
	// get details of member to be deleted
	$member = TaskService::getInstance($w)->getMemberById($p['id']);

	// build a static form displaying members details for confirmation of delete
	$f = Html::form(array(
		array("Member Details", "section"),
		array("", "hidden", "is_active", "1"),
		array("Name", "static", "name", TaskService::getInstance($w)->getUserById($member->user_id)),
		array("Role", "static", "role", $member->role)
	), $w->localUrl("/task-group/deletegroupmember/" . $member->id), "POST", " Delete ");

	// display form
	$w->setLayout(null);
	$w->ctx("deletegroupmember", $f);
}

function deletegroupmember_POST(Web &$w)
{
	$p = $w->pathMatch("id");
	// get the details of the person to delete 
	$member = TaskService::getInstance($w)->getMemberById($p['id']);
	// get the task group ID for returning to group display
	$tgid = $member->task_group_id;

	// if member exists, delete them
	if (!empty($member)) {
		// set is_active = 1
		$member->delete();

		// get group details, if person being deleted is the task group default assignee
		// set default_assigne_id = 0, ie noone. owners can edit task group and assign default assignee at any time
		$group = TaskService::getInstance($w)->getTaskGroup($tgid);
		if ($member->user_id == $group->default_assignee_id) {
			$group->default_assignee_id = 0;
			$group->update();
		}
		// return
		$w->msg("Task Group updated", "/task-group/viewmembergroup/" . $tgid);
	} else {
		// if member somehow no longer exists, say as much
		$w->msg("Task Group Members no longer exists?", "/task-group/viewmembergroup/" . $tgid);
	}
}
