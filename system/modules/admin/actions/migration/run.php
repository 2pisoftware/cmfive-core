<?php

function run_GET(Web $w)
{

	$prevpageurlextension = Request::string('prevpage');

	$p = $w->pathMatch("module", "file");

	if (empty($p['module'])) {
		$w->error("Missing module parameter required to run migration", "/admin-migration");
	}

	$response = MigrationService::getInstance($w)->runMigrations($p['module'], $p['file'], (Request::string('ignoremessages') != "false"), (Request::string('continuingrunall') == "true"));

	$w->msg($response, "/admin-migration#" . $prevpageurlextension);
}
