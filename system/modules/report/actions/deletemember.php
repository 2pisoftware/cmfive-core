<?php
function deletemember_GET(Web &$w) {
	$p = $w->pathMatch("report_id","user_id");

	// get details of member to be deleted
	$member = $w->Report->getReportMember($p['report_id'],$p['user_id']);

	if ($member) {
		// build a static form displaying members details for confirmation of delete
		$f = Html::form(array(
		array(__("Confirm Delete Member"),"section"),
		array("","hidden", "is_deleted","1"),
		array(__("Name"),"static", "name", $w->Report->getUserById($member->user_id)),
		),$w->localUrl("/report/deletemember/".$member->report_id."/".$member->user_id),"POST",__(" Delete "));
	}
	else {
		$f = __("No such member?");
	}
	// display form
	$w->setLayout(null);
	$w->ctx("deletemember",$f);
}

function deletemember_POST(Web &$w) {
	$p = $w->pathMatch("report_id","user_id");
	// get the details of the person to delete
	$member = $w->Report->getReportMember($p['report_id'],$p['user_id']);
	$_POST['id'] = $member->id;

	// if member exists, delete them
	if ($member) {
		$member->fill($_POST);
		$member->update();

		$w->msg(__("Member deleted"),"/report/viewreport/".$p['report_id']."?tab=2");
	}
	else {
		// if member somehow no longer exists, say as much
		$w->msg(__("Member no longer exists?"),"/report/edit/".$p['report_id']."?tab=2");
	}
}
