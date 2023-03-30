<?php
// edit a member
function editmember_GET(Web &$w) {
	$p = $w->pathMatch("repid","userid");
	// get member details for edit
	$member = ReportService::getInstance($w)->getReportMember($p['repid'], $p['userid']);

	// build editable form for a member allowing change of membership type
	$f = Html::form(array(
		array("Member Details","section"),
		array("","hidden", "report_id",$p['repid']),
		array("Name","static", "name", ReportService::getInstance($w)->getUserById($member->user_id)),
		array("Role","select","role",$member->role,ReportService::getInstance($w)->getReportPermissions()),
		array("Is email recipient", "checkbox", "is_email_recipient", $member->is_email_recipient)
	),$w->localUrl("/report/editmember/".$p['userid']),"POST"," Update ");

	// display form
	$w->setLayout(null);
	$w->ctx("editmember",$f);
}

function editmember_POST(Web &$w) {
	$p = $w->pathMatch("id");
	$member = ReportService::getInstance($w)->getReportMember($_POST['report_id'], $p['id']);

	$member->fill($_POST);
	$member->is_email_recipient = intval(!empty($_POST['is_email_recipient']));
	$member->update();

	$w->msg("Member updated","/report/edit/".$_POST['report_id']."#members");
}
