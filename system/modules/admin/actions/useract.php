<?php
function useract_GET(Web &$w) {
	$w->pathMatch("id","active");
	$user = $w->auth->getObject("User",$w->ctx("id"));
	if ($user) {
		$user->is_active = $w->ctx("active");
		$user->update();
		$w->msg(__("User ").$user->login." ".($user->is_active ? "activated" : __("suspended")),"/admin/users");
	} else {
		$w->error(__("User ").$w->ctx("id").__(" does not exist."),"/admin/users");
	}

}
