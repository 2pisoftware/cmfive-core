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

	$module = $w->request('module');
	$name = $w->request('name');

	if (empty($module) || empty($name)) {
		$w->error('Missing data', '/admin-migration#seed');
	}

	$response = $w->Migration->createMigrationSeed($module, $name);

	if ($response) {
		$w->msg('Migration seed created', '/admin-migration#seed');
	} else {
		$w->error('Migration seed creation failed', '/admin-migration#seed');
	}
}