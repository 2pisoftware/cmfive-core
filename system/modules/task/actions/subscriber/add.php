<?php

function add_GET(Web $w) {
	
	$w->setLayout(null);
	list($task_id) = $w->pathMatch();

	if (empty($task_id)) {
		$w->error("Task ID not found", "/task");
	}

	$w->out(Html::multiColForm([
		"Add an existing contact" => [
			[["Contact", "autocomplete", "contact", null, $w->Auth->getContacts()]]
		],
		"Or add an external user" => [
			[
				["Firstname", "text", 'firstname'],
				['Lastname', 'text', 'lastname']
			],
			[['Email', 'text', 'email']]
		]
	], '/task-subscriber/add/' . $task_id));

}

function add_POST(Web $w) {

}