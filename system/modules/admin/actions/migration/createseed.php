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
						'selected_option' => Request::string('active-module') ?? null,
						'options' => $w->modules(),
						'required' => true
					])),
				],
				[
					(new \Html\Form\InputField([
						"id|name" => "name",
						'label' => 'Name',
						'required' => true
					]))
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

	// split on spaces ensure camel case
	$name = implode("", array_map('ucfirst', preg_split('/\s+/', $name)));

	// ensure name is a valid php class name (excluding php keyword restrictions)
	// if there are invalid characters, underline them in the error message banner
	// valid class name regex: /^[a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*$/
	// see: https://www.php.net/manual/en/language.oop5.basic.php
	if (!preg_match('/^[a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*$/', $name)) {
		$invalid_character_regex = '/(^[^a-zA-Z_\x80-\xff])|([^a-zA-Z0-9_\x80-\xff])/';

		// Wrap all invalid characters with a span to apply the red squiggly underline class
		$name = preg_replace_callback(
			$invalid_character_regex,
			function ($matches) {
				return '<span class="red-squiggly-underline">' . $matches[0] . '</span>';
			},
			$name
		);

		$w->error("Invalid database seed name: " . $name, "/admin-migration#seed");
	}

	$response = MigrationService::getInstance($w)->createMigrationSeed($module, $name);

	if ($response) {
		$w->msg('Migration seed created', '/admin-migration#seed');
	} else {
		$w->error('Migration seed creation failed', '/admin-migration#seed');
	}
}