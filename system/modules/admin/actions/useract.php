<?php
function useract_GET(Web &$w) {
	$w->pathMatch("id","active");
	$user = AuthService::getInstance($w)->getObject("User",$w->ctx("id"));
	if ($user) {
		$user->is_active = $w->ctx("active");
		$user->update();
		$w->msg("User ".$user->login." ".($user->is_active ? "activated" : "suspended"),"/admin/users");
	} else {
		$w->error("User ".$w->ctx("id")." does not exist.","/admin/users");
	}

}