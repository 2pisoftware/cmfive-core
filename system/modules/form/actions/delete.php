<?php

function delete_ALL(Web $w) {
	
	$p = $w->pathMatch("id");
	if (empty($p['id'])) {
		$w->error("Form not found", "/form");
	}
	
	$_form_object = $w->Form->getForm($p['id']);
	if (empty($_form_object->id)) {
		$w->error("Form not found", "/form");
	}
	
	$_form_object->delete();
	$w->msg("Form deleted", "/form");
}