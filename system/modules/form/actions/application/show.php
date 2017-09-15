<?php

function show_GET(Web $w) {

	list($id) = $w->pathMatch('id');

	$application = $w->FormApplication->getFormApplication($id);

	$w->ctx('application', $application);

}