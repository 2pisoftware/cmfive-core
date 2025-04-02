<?php

function reassign_GET(Web $w) {
	
	list($user_id) = $w->pathMatch();
	$redirect = Request::string("redirect");
	
	if (empty($user_id)) {
		$w->error("No user specified", $redirect ? : "/admin/users");
	}
	
	// Get a list of users whoare report_admins/editors and aren't the person being reassigned
	$report_editors = array_filter(AuthService::getInstance($w)->getUsers() ? : [], function($user) use ($user_id) {
		return $user->hasAnyRole(["report_admin", "report_editor"]) && ($user->id != $user_id);
	});
	
	// Print form
	$w->out(HtmlBootstrap5::multiColForm([
		"Reassign reports" => [
			[["Reassign reports to", "select", "reassign_to", null, $report_editors]]
		]
	], '/report-user/reassign/' . $user_id . '?redirect=' . $redirect));
	
}

function reassign_POST(Web $w) {
	
	list($user_id) = $w->pathMatch();
	$redirect = Request::string("redirect");
	
	if (empty($user_id)) {
		$w->error("No user specified", $redirect ? : "/admin/users");
	}
	
	// Get the user to reassign to
	$reassign_user = AuthService::getInstance($w)->getUser($_POST['reassign_to']);
	if (empty($reassign_user->id)) {
		$w->error("Reassign to user not found", $redirect ? : "/admin/users");
	}
	
	// Get reports that a user owns
	$reports = $w->db->get("report")->leftJoin("report_member on report_member.report_id = report.id")
			->where("report_member.user_id", $user_id)
			->where("report.is_deleted", 0)
			->where("report_member.is_deleted", 0)->fetchAll();
	
	if (!empty($reports)) {
		$report_objects = ReportService::getInstance($w)->getObjectsFromRows("Report", $reports);
		foreach($report_objects as $report) {
			// Get the user to removes membership
			$user_membership = ReportService::getInstance($w)->getObject("ReportMember", ["user_id" => $user_id, "report_id" => $report->id, "is_deleted" => 0]);
			if (empty($user_membership->id)){
				continue;
			}
			
			// Try and get a membership from the reassign user for the current report
			$potential_assignee = ReportService::getInstance($w)->getObject("ReportMember", ["user_id" => $reassign_user->id, "report_id" => $report->id, "is_deleted" => 0]);
			if (!empty($potential_assignee->id)) {
				// Force existing member to be an owner
				$potential_assignee->role = ($potential_assignee->role != "OWNER" && $user_membership->role != "GUEST" ? $potential_assignee->role = $user_membership->role : $potential_assignee->role);
				$potential_assignee->update();
				
				// Revoke old membership
				$user_membership->delete();
			} else {
				// Reassign the user membership to the reassignee
				$user_membership->user_id = $reassign_user->id;
				$user_membership->update();
			}
		}
	} else {
		LogService::getInstance($w)->warn("No reports found for user " . $user_id);
	}
	
	$w->msg("Users reports reassigned to " . $reassign_user->getFullName(), $redirect ? : "/admin/users");
	
}
