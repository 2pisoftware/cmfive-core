<?php

function index_ALL(Web $w) {
	
	$w->ctx("title", "Forms list");
	$forms = $w->Form->getForms();
	
	$w->ctx("forms", $forms);
	
}