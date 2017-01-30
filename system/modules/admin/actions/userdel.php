<?php
function userdel_GET(Web $w) {
	$w->pathMatch("id");
	$user = $w->auth->getObject("User",$w->ctx("id"));
	if ($user) {
		$user->is_deleted = 1;
		$user->update();
		$w->msg(__("User ").$user->login.__(" deleted."),"/admin/users");
	} else {
		$w->error(__("User ").$w->ctx("id").__(" does not exist."),"/admin/users");
	}

}
