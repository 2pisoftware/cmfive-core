<?php

function create_GET(Web $w) {
	
	$p = $w->pathMatch("module");
	
	if (empty($p['module']) || !in_array($p['module'], $w->modules())) {
		$w->out(__("Missing specified module or it doesn't exist"));
	}
	
	$form = [
		__("Enter the migration name (camel case)") => [
			[[__("Name"), "text", "name"]]
		]
	];
	
	$w->out(Html::multiColForm($form, "/admin-migration/create/" . $p['module']));
	
}

function create_POST(Web $w) {
	
	$p = $w->pathMatch("module");
	
	if (empty($p['module']) || !in_array($p['module'], $w->modules())) {
		$w->error(__("Missing specified module or it doesn't exist"), "/admin-migration");
	}
	
	$name = $w->request('name', 'migration');
	
	$response = $w->Migration->createMigration($p['module'], $name);
	
	$w->msg($response, "/admin-migration");
	
}
