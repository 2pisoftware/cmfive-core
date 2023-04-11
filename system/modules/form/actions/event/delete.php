<?php

function delete_ALL(Web $w) {
	$p = $w->pathMatch("id");
	if (empty($p['id'])) {
		$w->error("Form Event not found", "/form");
	}
	$form_event = FormService::getInstance($w)->getFormEvent($p['id']);
	if (!empty($form_event)) {
		$form_event->delete();
		$w->msg("Form Event deleted",'/form/show/' . $event->form_id);
	}
	$w->error("No Event found for id",'/form');
}