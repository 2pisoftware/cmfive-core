<?php

function export_ALL(Web $w) {
	$p = $w->pathMatch('id');
	if (empty($p['id'])) {
		$w->error("No form id found", "/form/index");
	}
	$application = FormService::getInstance($w)->getFormApplication($p['id']);
	if (empty($application)) {
		$w->error("No application found for id", "/form/index");
	}
	$export = [
		'title' => StringSanitiser::sanitise($application->title),
		'description' => StringSanitiser::sanitise($application->description),
		'forms' => []
	];
	$forms = $application->getForms();
	if (!empty($forms)) {
		foreach ($forms as $form) {
			$exp_form = FormService::getInstance($w)->getFormForExport($form->id);
			$export['forms'][] = $exp_form;
		}
	}

	$application_title = preg_replace('/[^A-Za-z0-9\-]/', '', $application->title);
	$export_json = json_encode($export);
	$zip = new ZipArchive();
	$zip_name = ($application_title ?? "export-" . formatDate(time(), 'Y-m-d')) .".zip"; // Zip name
	
	$zip->open($zip_name,  ZipArchive::CREATE);
	$zip->addFromString($application_title,  $export_json);  
	$zip->close();
	
	header('Content-Type: application/zip');
	header('Content-disposition: attachment; filename="'.$zip_name.'"');
	header('Content-Length: ' . filesize($zip_name));
	readfile($zip_name);
}
