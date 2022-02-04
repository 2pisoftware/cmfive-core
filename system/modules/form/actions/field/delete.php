<?php

function delete_ALL(Web $w) {
	
	$p = $w->pathMatch("id");
	if (empty($p['id'])) {
		$w->error("Form field not found", "/form");
	}
	
	$_form_field_object = FormService::getInstance($w)->getFormField($p['id']);
	$form=$_form_field_object->getForm();
	if (empty($form->id)) {
		$w->error("Form not found", "/form");
	}
	
	
	if (empty($_form_field_object->id)) {
		$w->error("Form field not found", "/form/show/".$form->id);
	}
	
	$_form_field_object->delete();
	$w->msg("Form field deleted", "/form/show/".$form->id);
}
