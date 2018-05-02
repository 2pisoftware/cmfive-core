<?php

function index_GET(Web $w) {

	$w->ctx('title', 'Form Applications');
	$applications = $w->FormApplication->getFormApplications();

	$application_table_data = [];
	if (!empty($applications)) {
		foreach($applications as $application) {
			$application_table_data[] = [
				Html::a('/form-application/show/' . $application->id, $application->title),
				$application->description,
				$application->is_active == 1 ? 'Active' : 'Inactive',
				Html::b('/form-application/edit/' . $application->id, 'Edit') .
				Html::b('/form-application/delete/' . $application->id, 'Delete', 'Are you sure you want to delete this application? All references to already entered data will be lost!', null, false, "warning")
			];
		}
	}

	$w->ctx('application_table_header', ['Title', 'Description', 'Active', 'Actions']);
	$w->ctx('application_table_data', $application_table_data);
	
}