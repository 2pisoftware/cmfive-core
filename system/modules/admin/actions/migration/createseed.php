<?php

use Html\Form\Select;

function createseed_GET(Web $w) {
	$w->setLayout('layout-bootstrap-5');

	$w->out(
		HtmlBootstrap5::multiColForm([
			'Create a seed' => [
				[
					(new Select([
						"id|name" => "module",
						'label' => 'Module',
						'selected_option' => null,
						'options' => $w->modules(),
						'required' => true
					])), //["Module", "select", "module", null, $w->modules()]],
				],
				[
					(new \Html\Form\InputField([
						"id|name" => "name",
						'label' => 'Name',
						'required' => true
					])) //"Name", "text", "name"]]
				]
			]
		], '/admin-migration/createseed'));
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