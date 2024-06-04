<?php
function userdel_GET(Web $w) {
	$w->pathMatch("id");
	$user = AuthService::getInstance($w)->getObject("User", $w->ctx("id"));
	if ($user) {
		$user->delete();

		if ($w->session('user_id') == $w->ctx("id")) {
			// We deleted our own user, force logout
			$w->sessionDestroy();
			$w->redirect($w->localUrl("/auth/login"));
		} else {
			$w->msg("User " . $user->login . " deleted.", "/admin/users");
		}
	} else {
		$w->error("User " . $w->ctx("id") . " does not exist.", "/admin/users");
	}
}
