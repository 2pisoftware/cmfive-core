<?php
// provide form by which to add members to a report
function addmembers_GET(Web &$w) {
	$p = $w->pathMatch("id");

	// get the list of report editors and admins
	$members1 = $w->Auth->getUsersForRole("report_editor");
	$members2 = $w->Auth->getUsersForRole("report_user");
	// merge into single array
	$members12 = array_merge($members1, $members2);

	// strip the dumplicates. dealing with an object so no quick solution
	$members = array();
	foreach ($members12 as $member) {
		if (!in_array($member, $members)) {
			$members[] = $member;
		}
	}

	// build form
	$addUserForm = array(
	array("","hidden", "report_id",$p['id']),
	array("Add Member","select","member",null,$members),
	array("With Role","select","role","",$w->Report->getReportPermissions()),
	);

	$w->setLayout(null);
	$w->ctx("addmembers",Html::form($addUserForm,$w->localUrl("/report/updatemembers/"),"POST"," Submit "));
}