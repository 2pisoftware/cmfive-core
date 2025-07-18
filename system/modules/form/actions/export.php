<?php

function export_ALL(Web $w) {
	$p = $w->pathMatch('id');
	if (empty($p['id'])) {
		$w->error("No form id found", "/form/index");
	}
	$form = FormService::getInstance($w)->getForm($p['id']);
	if (empty($form)) {
		$w->error("No form found for id", "/form/index");
	}

	$export = FormService::getInstance($w)->getFormForExport($form->id);

	$export_json = json_encode($export);
	$form_title = preg_replace('/[^A-Za-z0-9\-]/', '', $form->title);
	$zip = new ZipArchive();
	$zip_name = $form_title .".zip"; // Zip name

	$zip->open($zip_name,  ZipArchive::CREATE);
	
	$zip->addFromString($form_title,  $export_json);  
	  
	
	$zip->close();

	header('Content-Type: application/zip');
	header('Content-disposition: attachment; filename="'.$zip_name.'"');
	header('Content-Length: ' . filesize($zip_name));
	readfile($zip_name);

}

