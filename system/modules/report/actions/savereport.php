<?php
// save newly created form
function savereport_POST(Web &$w) {
	ReportService::getInstance($w)->navigation($w, "Create Report");

	// get type of statement: select/insert/update/delete
	$_POST['sqltype'] = ReportService::getInstance($w)->getSQLStatementType($_POST['report_code']);

	// insert report into database
	$report = new Report($w);
	$report->fill($_POST);
	$report->insert();

	// if insert successful, make creator a MEMBER of this report
	if ($report->id) {
		$arr['report_id'] = $report->id;
		$arr['user_id'] = $w->session('user_id');

		$mem = new ReportMember($w);
		$mem->fill($arr);
		$mem->insert();
	}
	$w->msg("Report created","/report/index/");
}