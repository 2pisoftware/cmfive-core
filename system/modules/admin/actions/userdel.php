<?php
function userdel_GET(Web $w) {
	$w->pathMatch("id");
	$user = $w->auth->getObject("User",$w->ctx("id"));
	if ($user) {
		$user->delete();
		$w->msg("User ".$user->login." deleted.","/admin/users");
	} else {
		$w->error("User ".$w->ctx("id")." does not exist.","/admin/users");
	}

}