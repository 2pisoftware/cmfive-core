<?php

function edit_GET(Web $w) {

	$w->setLayout(null);
	list($id) = $w->pathMatch('id');

	$application = !empty($id) ? $w->FormApplication->getFormApplication($id) : new FormApplication($w);

	$form = [
		"Application" => [
			[(new \Html\Form\InputField())->setLabel('Title')->setName('title')->setValue($application->title)->setRequired(true)],
			[(new \Html\Form\Textarea())->setLabel('Description')->setName('description')->setValue($application->description)],
			[(new \Html\Form\InputField\Checkbox())->setLabel('Active')->setName('is_active')->setChecked($application->is_active)]
		]
	];

	$w->out(Html::multiColForm($form, '/form-application/edit/' . $application->id));

}

function edit_POST(Web $w) {

	$w->setLayout(null);
	list($id) = $w->pathMatch('id');

	$application = !empty($id) ? $w->FormApplication->getFormApplication($id) : new FormApplication($w);
	$application->fill($_POST);
	$application->is_active = !empty($_POST['is_active']);
	$application->insertOrUpdate();

	$w->msg('Application ' . (!empty($id) ? 'updated' : 'created'), '/form-application');

}