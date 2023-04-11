<?php

function rollback_GET(Web $w) {
	$w->setLayout(null);
	$p = $w->pathMatch("module", "file");
	
	if (empty($p['module']) || empty($p['file'])) {
		$w->error("Missing parameters required for rollback", "/admin-migration");
	}
	
	$response = MigrationService::getInstance($w)->rollback($p['module'], $p['file']);
	
	$w->msg($response, "/admin-migration#individual");
	
}