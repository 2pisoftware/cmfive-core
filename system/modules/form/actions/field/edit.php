<?php

function edit_GET(Web $w) {
	
	$p = $w->pathMatch("id");
	$form_id = $w->request("form_id");
	
	if (empty($form_id)) {
		$w->error("Form not found", "/form");
	}

	VueComponentRegister::registerComponent('metadata-subform', new VueComponent('metadata-subform', '/system/modules/form/assets/js/metadata-subform.vue.js'));
	VueComponentRegister::registerComponent('metadata-select', new VueComponent('metadata-select', '/system/modules/form/assets/js/metadata-select.vue.js', '/system/modules/form/assets/js/metadata-select.vue.css'));
	
	$_form_field_object = $p['id'] ? $w->Form->getFormField($p['id']) : new FormField($w);
	$w->ctx('title', (!empty($_form_field_object->id) ? 'Edit' : 'Create') . ' form field');
	$w->ctx("form_id", $form_id ? : $_form_field_object->form_id);
	$w->ctx("field", $_form_field_object);
}

function edit_POST(Web $w) {
	
	$p = $w->pathMatch("id");
	$form_id = $w->request("form_id");
	
	$_form_field_object = $p['id'] ? $w->Form->getFormField($p['id']) : new FormField($w);
	
	$_form_field_object->name 			= $w->request('name');
	$_form_field_object->technical_name = $w->request('technical_name');
	$_form_field_object->type 			= $w->request('type');
	$_form_field_object->form_id 		= intval($form_id);
	$_form_field_object->insertOrUpdate();
	
	// Clear post vars ready for saving metadata
	unset($_POST[CSRF::getTokenID()]);
	unset($_POST['name']);
	unset($_POST['technical_name']);
	unset($_POST['type']);
	
	if (!empty($p['id'])) {
		$saved_metadata = $_form_field_object->getMetadata();
		if (!empty($saved_metadata)) {
			foreach($saved_metadata as $_metadata) {
				if (array_key_exists($_metadata->meta_key, $_POST)) {
					$_metadata->meta_value = $_POST[$_metadata->meta_key];
					$_metadata->update();
					unset($_POST[$_metadata->meta_key]);
				} else {
					$_metadata->delete();
				}
			}
		}
	}
	
	if (!empty($_POST)) {
		foreach($_POST as $key => $value) {
			$new_metadata = new FormFieldMetadata($w);
			$new_metadata->form_field_id = $_form_field_object->id;
			$new_metadata->meta_key = $key;
			$new_metadata->meta_value = $value;
			$new_metadata->insert();
		}
	}
	
	$w->msg("Form " . ($p['id'] ? 'updated' : 'created'), "/form/show/" . $_form_field_object->form_id);
}
