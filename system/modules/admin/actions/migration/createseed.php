<?php

function createseed_GET(Web $w) {
	$w->setLayout(null);

	$w->out(
		Html::multiColForm([
			'Create a seed' => [
				[["Module", "select", "module", null, $w->modules()]],
				[["Name", "text", "name"]]
			]
		], '/admin-migration/createseed')
	);
}

function createseed_POST(Web $w) {

	$module = Request::string('module');
	$name = Request::string('name');

	if (empty($module) || empty($name)) {
		$w->error('Missing data', '/admin-migration#seed');
	}

	$response = MigrationService::getInstance($w)->createMigrationSeed($module, $name);

	if ($response) {
		$w->msg('Migration seed created', '/admin-migration#seed');
	} else {
		$w->error('Migration seed creation failed', '/admin-migration#seed');
	}
}