<?php

function create_GET(Web $w) {
	$p = $w->pathMatch("module");
	
	if (empty($p['module']) || !in_array($p['module'], $w->modules())) {
		$w->out("Missing specified module or it doesn't exist");
	}
	
	$form = [
		'Enter the migration name (camel case)' => [
			[
				(new \Html\Form\InputField([
					"id|name" => "name",
					'label' => 'Name',
					'required' => true
				])) //"Name", "text", "name"]]
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

	// split on spaces, periods, and dashes, and ensure camel case
	$name = implode("", array_map('ucfirst', preg_split('/[\s\.\-]/', $name)));

	// ensure name is a valid php class name (excluding php keyword restrictions)
	// if there are invalid characters, underline them in the error message banner
	// valid class name regex: /^[a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*$/
	// see: https://www.php.net/manual/en/language.oop5.basic.php
	if (!preg_match('/^[a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*$/', $name)) {
		$invalid_character_regex = '/(^[^a-zA-Z_\x80-\xff])|([^a-zA-Z0-9_\x80-\xff])/';

		// Wrap all invalid characters with a span to apply the red squiggly underline class
		$name = preg_replace_callback(
			$invalid_character_regex, function ($matches) {
			return '<span class="red-squiggly-underline">' . $matches[0] . '</span>';
		}, $name);

		$w->error("Invalid migration name: " . $name, "/admin-migration#individual");
	}

	$response = MigrationService::getInstance($w)->createMigration($p['module'], $name);
	
	$w->msg($response, "/admin-migration#individual");
}