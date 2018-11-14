<?php

function manage_subform_GET(Web $w) {
	
	list($id) = $w->pathMatch('id');

	if (empty($id)) {
		$w->ctx('error_message', 'No ID given');
	}

	$form_value = $w->Form->getFormValue($id);
	if (empty($form_value->id)) {
		$w->ctx('error_message', 'Subform not found');
	}

	// Check that value is a subform
	$field = $form_value->getFormField();
	if ($field->type !== "subform") {
		// Handle issue with non subform
	}

	$metadata = $field->getMetadata();
	if (empty($metadata)) {
		// Handle issue with missing metadata
		$w->out("Subform not found");
		return;
	}

	$subform = null;
	foreach($metadata as  $metadata_row) {
		if ($metadata_row->meta_key === "associated_form") {
			$subform = $w->Form->getForm($metadata_row->meta_value);
		}
	}


	if (empty($subform)) {
		// Handle issue with missing form
		$w->out("Subform not found");
		return;
	}

	$w->ctx('subform', $subform);
	$w->ctx("form_value", $form_value);
	$w->ctx('display_only', !!$w->request('display_only'));
}