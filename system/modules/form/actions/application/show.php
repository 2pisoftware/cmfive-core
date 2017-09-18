<?php

function show_GET(Web $w) {

	list($id) = $w->pathMatch('id');

	$w->ctx('title', 'Form Application');

	$application = $w->FormApplication->getFormApplication($id);

	$w->ctx('application', $application);

}