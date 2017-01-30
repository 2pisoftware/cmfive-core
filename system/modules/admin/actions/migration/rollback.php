<?php

function rollback_GET(Web $w) {
	$w->setLayout(null);
	$p = $w->pathMatch("module", "file");
	
	if (empty($p['module']) || empty($p['file'])) {
		$w->error(__("Missing parameters required for rollback"), "/admin-migration");
	}
	
	$response = $w->Migration->rollback($p['module'], $p['file']);
	
	$w->msg($response, "/admin-migration#" . $p['module']);
	
}
