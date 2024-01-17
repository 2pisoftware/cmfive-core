<?php

function remove_GET(Web $w) {

	$w->setLayout('layout-bootstrap-5');
	
	list($user_id) = $w->pathMatch();
	
	if (empty($user_id)) {
		$w->error("User not found", "/admin");
	}
	
	$user = AuthService::getInstance($w)->getUser($user_id);
	if (empty($user) || !$user->exists()) {
		$w->error("User not found", "/admin");
	}
	
	$w->ctx("title", 'Data clean up for ' . $user->getFullName());
	
	// Call a hook and display the output to screen
	$hook_results = $w->callHook("admin", "remove_user", $user);
	$hook_output = '';
	if (!empty($hook_results)) {
		$hook_output = array_reduce($hook_results, function($carry, $element) {
			return $carry .= strlen(trim($element ?? '')) ? '<div class=\'small-12 columns panel\'>' . $element . '</div>' : "";
		});
	}
	
	$w->ctx("user", $user);
	$w->ctx("hook_output", $hook_output);
}
