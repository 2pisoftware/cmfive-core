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
		$w->error("Missing specified module or it doesn't exist", "/admin-migration");
	}
	
	$name = Request::string('name', 'migration');

	$name = str_replace(' ', '', $name);

	$response = MigrationService::getInstance($w)->createMigration($p['module'], $name);
	
	$w->msg($response, "/admin-migration");
}