<?php

function show_GET(Web $w) {

	list($id) = $w->pathMatch('id');

	$w->ctx('title', 'Form Application');

	$w->enqueueStyle(['name' => 'form-style', 'uri' => '/system/modules/form/assets/css/form-style.css', 'weight' => 200]);

	$application = $w->FormApplication->getFormApplication($id);

	$w->ctx('application', $application);

}