<?php

function create_GET(Web $w) {
	$p = $w->pathMatch("module");
	
	if (empty($p['module']) || !in_array($p['module'], $w->modules())) {
		$w->out("Missing specified module or it doesn't exist");
	}
	
	$form = [
		'Enter the migration name' => [
			[
				(
					new \Html\Form\InputField(
						[
							"id|name" => "name",
							'label' => 'Name',
							'required' => true,
							'pattern' => '^[a-zA-Z_\x80-\xff][a-zA-Z0-9_\s+\x80-\xff]*$',
							'title' => 'Must be a letter or underscore followed by letters, numbers, underscores, or spaces'
						]
					)
				)
			]
		]
	];

	$w->out(HtmlBootstrap5::multiColForm($form, "/admin-migration/create/" . $p['module'], "POST", "Save", null, null, null, "_self", true, Migration::$_validation));
}

function create_POST(Web $w) {
	$p = $w->pathMatch("module");
	
	if (empty($p['module']) || !in_array($p['module'], $w->modules())) {
		$w->error("Missing specified module or it doesn't exist", "/admin-migration#individual");
	}
	
	$name = Request::string('name', 'migration');

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

		$w->error("Invalid migration name: " . $name, "/admin-migration#individual");
	}

	// remove whitespace, and ensure camel case
	$name = implode("", array_map('ucfirst', preg_split('/\s+/', $name)));


	$response = MigrationService::getInstance($w)->createMigration($p['module'], $name);
	
	$w->msg($response, "/admin-migration#individual");
}