<?php

use Html\Form\Select;

function createseed_GET(Web $w) {
	$w->setLayout('layout-bootstrap-5');

	$w->out(
		HtmlBootstrap5::multiColForm(
			[
				'Create a seed' => [
					[
						new Select(
							[
								"id|name" => "module",
								'label' => 'Module',
								'selected_option' => Request::string('default-selection') ?? null,
								'options' => $w->modules(),
								'required' => true
							]
						)
					],
					[
						new \Html\Form\InputField(
							[
								"id|name" => "name",
								'label' => 'Name',
								'required' => true,

								'pattern' => '^[a-zA-Z_\x80-\xff][a-zA-Z0-9_\s+\x80-\xff]*$',
								'title' => 'Must be a letter or underscore followed by letters, numbers, underscores, or spaces'
							]
						)
					]
				]
			],
			'/admin-migration/createseed'
		)
	);
}

function createseed_POST(Web $w) {

	$module = Request::string('module');
	$name = Request::string('name');

	if (empty($module) || empty($name)) {
		$w->error('Missing data', '/admin-migration#seed');
	}

	// matches a letter/underscore followed by letters/underscores/numbers/spaces
	$valid_string = '/^[a-zA-Z_\x80-\xff][a-zA-Z0-9_\s+\x80-\xff]*$/';

	// matches any character that would cause the string to be invalid
	$invalid_characters = '/(^[^a-zA-Z_\x80-\xff])|([^a-zA-Z0-9_\s+\x80-\xff])/';

	if (!preg_match($valid_string, $name)) {
		// wrap all invalid characters with an emphasis tag and apply the red squiggly underline class
		$name = preg_replace_callback(
			$invalid_characters,
			function ($matches) {
				return '<em class="red-squiggly-underline" aria-invalid="true" aria-live="polite">' . $matches[0] . '</em>';
			},
			$name
		);

		$w->error('Invalid migration name: ' . $name, '/admin-migration#individual');
	}

	// remove whitespace, and ensure camel case
	$name = implode('', array_map('ucfirst', preg_split('/\s+/', $name)));

	$response = MigrationService::getInstance($w)->createMigrationSeed($module, $name);

	if ($response) {
		$w->msg('Migration seed created', '/admin-migration#seed');
	} else {
		$w->error('Migration seed creation failed', '/admin-migration#seed');
	}
}