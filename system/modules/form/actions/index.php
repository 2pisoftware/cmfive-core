<?php

function index_ALL(Web $w) {
	
	$w->ctx("title", "Forms list");
	$forms = FormService::getInstance($w)->getForms();
	
	$w->ctx("forms", $forms);
	
}