<?php

function run_GET(Web $w) {
	
	$p = $w->pathMatch("module", "file");
	
	if (empty($p['module'])) {
		$w->error("Missing module parameter required to run migration", "/admin-migration");
	}
	
	$response = $w->Migration->runMigrations($p['module'], $p['file']);
	
	$w->msg($response, "/admin-migration");
	
}