<?php namespace System\Modules\Report;

function removeUser(\Web $w, $params = []) {
	
	$redirect = defaultVal($params['redirect']);
	$user = defaultVal($params['user']);
	
	if (empty($user)) {
		$w->error("No User provided", $redirect ? : "/admin/users");
	}
	
	// Get reports that a user owns
	$report_member = $w->db->get("report_member")->leftJoin("report on report_member.report_id = report.id")
			->where("report_member.user_id", $user->id)
			->where("report.is_deleted", 0)
			->where("report_member.is_deleted", 0)->fetchAll();
	
	$owned_reports = array_filter($report_member ? : [], function($instance) {
		return strtoupper($instance['role']) === "OWNER";
	});
	
	$report_member = array_filter($report_member ? : [], function($instance) {
		return strtoupper($instance['role']) !== "OWNER";
	});
	
	$w->ctx("owned_reports", $owned_reports);	
	$w->ctx("member_reports", $report_member);
	$w->ctx("user", $user);
	
}
