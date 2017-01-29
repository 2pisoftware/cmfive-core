<?php

function ajaxGetMetadata_GET(Web $w) {
	
	$p = $w->pathMatch("id");
	$type = $w->request("type");
	
	if (empty($p['id']) && empty($type)) {
		header("HTTP/1.1 404 Not Found");
		return;
	}
	
	$field = null;
	if(!empty($type)) {
		$interfaces = Config::get('form.interfaces');
		if (!empty($interfaces)) {
			foreach($interfaces as $interface) {
				if ($interface::respondsTo($type)) {
					echo Html::form($interface::metadataForm($type));
				}
			}
		}
	} else {
		if (!empty($p['id'])) {
			$field = $w->Form->getFormField($p['id']);
			if(empty($field->id)) {
				header("HTTP/1.1 404 Not Found");
				return;
			}

			$metadata_form = $field->getMetadataForm();
			if (!empty($metadata_form)) {
				echo Html::form($metadata_form);
			}
		} else {
			header("HTTP/1.1 404 Not Found");
			return;
		}
	}
}