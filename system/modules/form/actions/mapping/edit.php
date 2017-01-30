<?php

function edit_POST(Web $w) {
	
	$form_id = $w->request("form_id");
	
	// Load current mappings
	$current_mappings = $w->Form->getObjects("FormMapping", ["form_id" => $form_id, "is_deleted" => 0]);
	
	// Cross reference them with ones that have been posted
	if (!empty($current_mappings)) {
		foreach($current_mappings as $mapping) {
			if(!array_key_exists($mapping->object, $_POST)) {
				// Hard delete because we dont really need to track soft deleted
				// But option is there in case we ever want to
				$mapping->delete(true);
			} else {
				// Insert
				 unset($_POST[$mapping->object]);
			}
		}
	}
	
	// Save new additions
	if (!empty($_POST)) {
		foreach($_POST as $to_map => $value) {
			$new_mapping = new FormMapping($w);
			$new_mapping->form_id = $form_id;
			$new_mapping->object = $to_map;
			$new_mapping->insert();
		}
	}
	
	
	$w->msg(__("Form mappings updated"), "/form/show/" . $form_id . "#mapping");
}
