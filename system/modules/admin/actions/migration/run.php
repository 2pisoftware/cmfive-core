<?php

function run_GET(Web $w) {

	$prevpageurlextension = $w->request('prevpage');

	$p = $w->pathMatch("module", "file");

	if (empty($p['module'])) {
		$w->error("Missing module parameter required to run migration", "/admin-migration");
	}

	$response = $w->Migration->runMigrations($p['module'], $p['file'], ($w->request('ignoremessages') != "false"), ($w->request('continuingrunall') == "true"));

	$w->msg($response, "/admin-migration#" . $prevpageurlextension);
	
}