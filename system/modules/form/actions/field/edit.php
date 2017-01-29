<?php

function edit_GET(Web $w) {
	
	$p = $w->pathMatch("id");
	$form_id = $w->request("form_id");
	
	if (empty($form_id)) {
		$w->error("Form not found", "/form");
	}
	
	$_form_field_object = $p['id'] ? $w->Form->getFormField($p['id']) : new FormField($w);
	
//	$form = [
//		["Name", "text", "name", $_form_field_object->name],
//		["Type", "select", "type", $_form_field_object->type, FormField::getFieldTypes()],
//	];
	
	$metadata_form = [];
	if (!empty($_form_field_object->id)) {
		$metadata_form = $_form_field_object->getMetadataForm();
		
		// Add saved metadata
		$field_metadata = $_form_field_object->getMetadata();
		if (!empty($field_metadata)) {
			foreach($field_metadata as $_metadata) {
				foreach($metadata_form as &$metadata_form_element) {
					if (in_array($_metadata->meta_key, array_values($metadata_form_element))) {
						$metadata_form_element[3] = $_metadata->meta_value;
					}
				}
			}
		}
		
//		if (!empty($metadata_form)) {
//			$form = array_merge($form, $metadata_form);
//		}
	}
	
	$w->ctx("form_id", $form_id ? : $_form_field_object->form_id);
	$w->ctx("field", $_form_field_object);
	$w->ctx("metadata_form", $metadata_form);
}

function edit_POST(Web $w) {
	
	$p = $w->pathMatch("id");
	$form_id = $w->request("form_id");
	
	$_form_field_object = $p['id'] ? $w->Form->getFormField($p['id']) : new FormField($w);
	
	$_form_field_object->name = $_POST['name'];
	$_form_field_object->type = $_POST['type'];
	
	// Clear post vars ready for saving metadata
	unset($_POST[CSRF::getTokenID()]);
	unset($_POST['name']);
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
	
	$_form_field_object->form_id = intval($form_id);
	$_form_field_object->insertOrUpdate();
	
	$w->msg("Form " . ($p['id'] ? 'updated' : 'created'), "/form/show/" . $_form_field_object->form_id);
}
