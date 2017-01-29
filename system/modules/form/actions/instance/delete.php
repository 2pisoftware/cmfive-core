<?php

function delete_GET(Web $w) {
	$p = $w->pathMatch("id");
	$redirect_url = $w->request("redirect_url");
	
	if (empty($p['id'])) {
		$w->error("Form instance not found", $redirect_url . "#".toSlug($form->title));
		return;
	}
	
	$instance = $w->Form->getFormInstance($p['id']);
	if (empty($instance->id)) {
		$w->error("Form instance not found", $redirect_url . "#".toSlug($form->title));
		return;
	}
	
	$instance->delete();
	$w->msg("Form instance deleted", $redirect_url . "#".toSlug($form->title));
}
