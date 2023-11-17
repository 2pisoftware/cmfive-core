<?php

function index_ALL(Web $w) {
	$w->setLayout('layout-bootstrap-5');

	$w->ctx("title", "Forms list");
	$forms = FormService::getInstance($w)->getForms();
	
	$w->ctx("forms", $forms);
}