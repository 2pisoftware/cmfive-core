<?php

function report_core_web_before_get(Web $w) {
	// build Navigation to Reports for current Module
	if ($w->Auth->loggedIn()) {
		$reports = $w->Report->getReportsforNav();
		if ($reports) {
			$w->ctx("reports",$reports);
		}
	}
}

// Admin user remove hook
function report_admin_remove_user(Web $w, User $user) {
	return $w->partial("removeUser", ["user" => $user, "redirect" => "/admin-user/remove/" . $user->id], "report");
}