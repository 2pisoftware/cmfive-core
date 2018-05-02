<?php

function edit_GET(Web $w) {

	list($id) = $w->pathMatch('id');

	$w->enqueueScript(['name' => 'vue-js', 'uri' => '/system/templates/js/vue.js', 'weight' => 200]);

	$application = null;
	if (empty($id)) {
		$application = new FormApplication($w);
		$application->insert();
		$w->redirect('/form-application/edit/' . $application->id);
	} else {
		$application = $w->FormApplication->getFormApplication($id);
	}

	$form = [
		"Application" => [
			[(new \Html\Form\InputField())->setLabel('Title')->setName('title')->setValue($application->title)->setRequired(true)],
			[(new \Html\Form\Textarea())->setLabel('Description')->setName('description')->setValue($application->description)],
			[(new \Html\Form\InputField\Checkbox())->setLabel('Active')->setName('is_active')->setChecked($application->is_active)]
		]
	];

	$available_forms = $w->Form->getForms();

	$w->ctx('available_forms', $available_forms);
	$w->ctx('application', $application);
	$w->ctx('new_application', !empty($application->id));
	$w->ctx('form', Html::multiColForm($form, '/form-application/edit/' . $application->id));

}

function edit_POST(Web $w) {

	$w->setLayout(null);
	list($id) = $w->pathMatch('id');

	$application = !empty($id) ? $w->FormApplication->getFormApplication($id) : new FormApplication($w);
	$application->fill($_POST);
	$application->is_active = !empty($_POST['is_active']);
	$application->insertOrUpdate();

	$w->msg('Application ' . (!empty($id) ? 'updated' : 'created'), '/form-application/show/' . $application->id);

}