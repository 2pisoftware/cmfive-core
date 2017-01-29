<?php

/**
 * Delete function to remove timelogs for a user
 * @param Web $w
 */
function delete_GET(Web $w) {
	$p = $w->pathMatch("id");
	$redirect = $w->request("redirect", '');
	
	// Check for parameter
	if (empty($p['id'])) {
		$w->error("No timelog identifier provided", "/timelog");
	}
	
	// Check for object
	$timelog = $w->Timelog->getTimelog($p['id']);
	if (empty($timelog->id)) {
		$w->error("Timelog not found", "/timelog");
	}
	
	// Check permissions
	if (!$timelog->canDelete($w->Auth->user())) {
		$w->error("You cannot delete Timelogs", "/timelog");
	}
	
	$timelog->delete();
	$w->msg("Timelog deleted", !empty($redirect) ? $redirect : "/timelog");
}