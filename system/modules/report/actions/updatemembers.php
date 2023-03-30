<?php
// add members to a report
function updatemembers_POST(Web &$w) {
	$arrdb = array();
	$arrdb['report_id'] = $_REQUEST['report_id'];
	$arrdb['role'] = $_REQUEST['role'];
	$arrdb['is_deleted'] = 0;
	 
	$member = $_REQUEST['member'];
	// for each selected member, complete population of input array

	$arrdb['user_id'] = $member;
	// find member against report ID
	$mem = ReportService::getInstance($w)->getReportMember($arrdb['report_id'], $arrdb['user_id']);

	// if no membership, create it, otherwise update and continue
	if (!$mem) {
		$mem = new ReportMember($w);
		$mem->fill($arrdb);
		$mem->insert();
	}
	else {
		$mem->fill($arrdb);
		$mem->update();
	}

	// return
	$w->msg("Member Group updated","/report/edit/".$arrdb['report_id']."#members");
}
