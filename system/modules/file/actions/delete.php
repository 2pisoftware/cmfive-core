<?php

function delete_GET(Web $w)
{
	$p = $w->pathMatch("id");
	$redirect_url = Request::string("redirect_url");
	$redirect_url = defaultVal($redirect_url, defaultVal($_SERVER["REQUEST_URI"], "/"));

	if (empty($p['id'])) {
		$w->error("Missing attachment ID", $redirect_url);
	}

	$attachment = FileService::getInstance($w)->getAttachment($p['id']);
	if (empty($attachment->id)) {
		$w->error("Attachment not found", $redirect_url);
	}

	$attachment->delete();
	$w->msg("Attachment deleted", $redirect_url);
}
