<?php

function delete_GET(Web $w) {

	list($id) = $w->pathMatch('id');

	if (empty($id)) {
		$w->error('No Application ID found', '/form-application');
	}

	$application = FormService::getInstance($w)->getFormApplication($id);
	if (empty($application->id)) {
		$w->error('Application not found', '/form-application');
	}

	$application->delete();

	$w->msg('Application deleted', '/form-application');

}