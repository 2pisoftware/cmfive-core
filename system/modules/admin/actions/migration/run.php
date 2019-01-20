<?php

function run_GET(Web $w) {
	
	$p = $w->pathMatch("module", "file");

	// Check if the migration run call has been flagged to ignore any pre text messages
	$ignoreMessages = $w->request('ignoremessages');
	if ($ignoreMessages == "false")
	{
		$ignoreMessages = false;
	} else {
		$ignoreMessages = true;
	}

	if (empty($p['module'])) {
		$w->error("Missing module parameter required to run migration", "/admin-migration");
	}
	
	$response = $w->Migration->runMigrations($p['module'], $p['file'], $ignoreMessages);

	$w->msg($response, "/admin-migration");
	
}