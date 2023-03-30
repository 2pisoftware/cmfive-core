<?php

function unassign_POST(Web $w) {
	
	$user_id = $w->pathMatch();
	$redirect = Request::string("redirect");
	
	$group_membership = AuthService::getInstance($w)->getObjects("GroupUser", ["user_id" => $user_id]);
	
	if (!empty($group_membership)) {
		foreach($group_membership as $group_member) {
			$group_member->delete();
		}
	}
	
	$w->msg("Group membership revoked", $redirect ? : "/admin");
}
