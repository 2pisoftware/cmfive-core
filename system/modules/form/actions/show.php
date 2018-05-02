<?php

function show_GET(Web $w) {
	
	$p = $w->pathMatch("id");
	if (empty($p['id'])) {
		$w->error("Form not found", "/form");
	}
	
	$w->enqueueStyle(["uri" => "/system/modules/form/assets/css/form-style.css", "weight" => 500]);
	
	VueComponentRegister::registerComponent('metadata-subform', new VueComponent('metadata-subform', '/system/modules/form/assets/js/metadata-subform.vue.js'));
	VueComponentRegister::registerComponent('metadata-select', new VueComponent('metadata-select', '/system/modules/form/assets/js/metadata-select.vue.js', '/system/modules/form/assets/js/metadata-select.vue.css'));
	
	$_form_object = $w->Form->getForm($p['id']);
	
	$w->ctx("title", "Form: " . $_form_object->printSearchTitle());
	$w->ctx("form", $_form_object);
	$w->ctx("fields", $_form_object->getFields());
}