<?php

function delete_GET(Web $w) {
	
	$p = $w->pathMatch("id");
	$redirect_url = $w->request("redirect_url");
	$redirect_url = defaultVal($redirect_url, defaultVal($_SERVER["REQUEST_URI"], "/"));
	
	if (empty($p['id'])) {
		$w->error(__("Missing attachment ID"), $redirect_url);
	}
	
	$attachment = $w->File->getAttachment($p['id']);
	if (empty($attachment->id)) {
		$w->error(__("Attachment not found"), $redirect_url);
	}
	
	$attachment->delete();
	$w->msg(__("Attachment deleted"), $redirect_url);
}

