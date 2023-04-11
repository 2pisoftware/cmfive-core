<?php

/**
 * Restores a soft deleted attachment
 * 
 * @param Web $w
 */
function restore_GET(Web $w) {
	
	list($attachment_id) = $w->pathMatch();
	
	$redirect_url = Request::string("redirect_url", "/file");
	
	if (empty($attachment_id)) {
		$w->error("No Attachment ID given", $redirect_url);
	}
	
	$attachment = FileService::getInstance($w)->getObject("Attachment", $attachment_id);
	
	if (empty($attachment->id)) {
		$w->error("Attachment not found", $redirect_url);
	}
	
	$attachment->is_deleted = 0;
	$attachment->update(false);
	
	$w->msg("Attachment restored", $redirect_url);
	
}