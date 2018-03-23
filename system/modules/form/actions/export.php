<?php

function export_ALL(Web $w) {
	$p = $w->pathMatch('id');
	if (empty($p['id'])) {
		$w->error("No form id found", "/form/index");
	}
	$form = $w->Form->getForm($p['id']);
	if (empty($form)) {
		$w->error("No form found for id", "/form/index");
	}
	$export = [
		"form_title" => $form->title,
		"description" => $form->description,
		"header_template" => $form->header_template,
		"row_template" => $form->row_template,
		"summary_template" => $form->summary_template,
		"form_fields" => [],
		"form_mappings" => []
	];
	$form_fields = $form->getFields();
	if (!empty($form_fields)) {
		$fields = [];
		foreach ($form_fields as $form_field) {
			$field = [
				"field_name" => $form_field->name,
				"technical_name" => $form_field->technical_name,
				"interface_class" => $form_field->interface_class,
				"type" => $form_field->type,
				"mask" => $form_field->mask,
				"ordering" => $form_field->ordering,
				"field_metadata" => [] 
			];
			$field_metadata = $form_field->getMetadata();
			if (!empty($field_metadata)) {
				$fmd_array = [];
				foreach ($field_metadata as $field_md) {
					$md_array = [
						"meta_key" => $field_md->meta_key,
						"meta_value" => $field_md->meta_value
					];
					$fmd_array[] = $md_array;
				}
				$field['field_metadata'] = $fmd_array;
			}
			$fields[] = $field;
		}
		$export['form_fields'] = $fields;
	}
	//copy form mapping
	$form_mappings = $w->Form->getFormMappingsForForm($form->id);
	if (!empty($form_mappings)) {
		$mappings = [];
		foreach ($form_mappings as $mapping) {
			$mappings[] = $mapping->object;
		}
		$export['form_mappings'] = $mappings;
	}

	$export_json = json_encode($export);
	$zip = new ZipArchive();
	$zip_name = $form->title .".zip"; // Zip name
	// echo $zip_name; die;
	$zip->open($zip_name,  ZipArchive::CREATE);
	
	$zip->addFromString($form->title,  $export_json);  
	  
	
	$zip->close();
	//$w->out($zip);
	header('Content-Type: application/zip');
	header('Content-disposition: attachment; filename='.$zip_name);
	header('Content-Length: ' . filesize($zip_name));
	readfile($zip_name);
	// echo "<pre>";
	// var_dump(json_encode($export)); die;
}

