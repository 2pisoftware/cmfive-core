<?php

function listprocessors_GET(Web $w) {
	$w->Channels->navigation($w, __("Processors List"));
	// Get all email, FTP, local processors
	$processors = $w->Channel->getAllProcessors();

	$w->ctx("processors", $processors);
}
