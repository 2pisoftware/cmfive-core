<?php

function edit_GET(Web $w) {
	
	$p = $w->pathMatch("id");
	$_form_object = $p['id'] ? $w->Form->getForm($p['id']) : new Form($w);
	
	$form = [
		__("Form") => [
			[[__("Title"), "text", "title", $_form_object->title]],
			[[__("Description"), "text", "description", $_form_object->description]],
		]
	];
	
	$w->out(Html::multiColForm($form, '/form/edit/' . $_form_object->id));
}

function edit_POST(Web $w) {
	
	$p = $w->pathMatch("id");
	$_form_object = $p['id'] ? $w->Form->getForm($p['id']) : new Form($w);
	
	$_form_object->fill($_POST);
	$_form_object->insertOrUpdate();
	
	$redirect_url = $w->request("redirect_url");
	$w->msg(__("Form ") . ($p['id'] ? __('updated') : __('created')), !empty($redirect_url) ? $redirect_url : "/form");
	
}
