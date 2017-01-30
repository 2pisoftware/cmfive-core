<?php
// clicking the 'More Info' button for a task group gives all details specific to this group
// including group attributes and group membership
function viewmembergroup_GET(Web $w) {
	$p = $w->pathMatch("id");

	// tab: Members
	// get all members in a task group given a task group ID
	$member_group = $w->Task->getMemberGroup($p['id']);

	// get the group attributes given a task group ID
	$taskgroup = $w->Task->getTaskGroup($p['id']);
	if (empty($taskgroup->id)) {
		$w->error("Taskgroup not found", '/task');
	}

	// put the group title into the page heading
	$w->Task->navigation($w, __("Task Group - ") . $group->title);

	History::add("Task Group: " . $taskgroup->title, null, $taskgroup);

	// set columns headings for display of members
	$line[] = array(__("Member"),__("Role"),"");

	// if their are members, display their full name, role and buttons to edit or delete the member
	if ($member_group) {
		foreach ($member_group as $member) {
			$line[] = array($w->Task->getUserById($member->user_id), $member->role,
				Html::box(WEBROOT . "/task-group/viewmember/" . $member->id, " Edit ", true) .
				"&nbsp;&nbsp;" .
				Html::box(WEBROOT . "/task-group/deletegroupmember/" . $member->id, " Delete ", true)
			);
		}
	} else {
		// if there are no members, say as much
		$line[] = array(__("Group currently has no members. Please Add New Members."), "", "");
	}

	// enter task group attributes sa query string for buttons providing group specific functions such as delete or add members
	$w->ctx("taskgroup",$group->task_group_type);
	$w->ctx("grpid",$group->id);
	$w->ctx("groupid",$p['id']);

	// display list of group members
	$w->ctx("viewmembers", Html::table($line, null, "tablesorter", true));
	
	// tab:  Notify
	$notify = $w->Task->getTaskGroupNotify($taskgroup->id);

	if ($notify) {
		foreach ($notify as $n) {
			$v[$n->role][$n->type] = $n->value;
		}
	} else {
		$v['guest']['creator'] = 0;
		$v['member']['creator'] = 0;
		$v['member']['assignee'] = 0;
		$v['owner']['creator'] = 0;
		$v['owner']['assignee'] = 0;
		$v['owner']['other'] = 0;
	}

	$notifyForm['Task Group Notifications'] = array(
			array(array("","hidden", "task_group_id",$group->id)),
			array(
					array("","static",""),
					array(__("Creator"),"static","creator"),
					array(__("Assignee"),"static","assignee"),
					array(__("All Others"),"static","others"),
			),
			array(
					array(__("Guest"),"static","guest"),
					array("","checkbox","guest_creator",$v['guest']['creator'])
			),
			array(
					array(__("Member"),"static","member"),
					array("","checkbox","member_creator",$v['member']['creator']),
					array("","checkbox","member_assignee",$v['member']['assignee']),
			),
			array(
					array(__("Owner"),"static","owner"),
					array("","checkbox","owner_creator",$v['owner']['creator']),
					array("","checkbox","owner_assignee",$v['owner']['assignee']),
					array("","checkbox","owner_other",$v['owner']['other']),
			),
	);

	$w->ctx("taskgroup", $taskgroup);
	$w->ctx("notifymatrix", Html::multiColForm($notifyForm, $w->localUrl("/task-group/updategroupnotify/")));
}
