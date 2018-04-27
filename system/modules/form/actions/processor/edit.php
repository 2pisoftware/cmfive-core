<?php

function edit_GET (Web $w) {
	$p = $w->pathMatch('id');
	if (empty($p['id'])) {
		$processor = new FormEventProcessor($w);
	} else {
		$processor = $w->Form->getEventProcessor($p['id']);
		if (empty($processor)) {
			$w->error('No processor found for id');
		}
	}
	$form_id = $w->request('form_id');
	if (empty($form_id)) {
		$w->error('No form id found');
	}
	$form = $w->Form->getForm($form_id);
	if (empty($form)) {
		$w->error('No form found for id');
	}

	$processor_options = $w->Form->getEventProcessorList();
	//create processor form 
	$form = [
		'Processor' => [
			[
				(new \Html\Form\Inputfield($w))->setLabel('Name')->setName('name')->setValue($processor->name)
			],
			[
				(new \Html\Form\Select($w))->setLabel('Event')->setName('form_event_id')->setOptions($form->getEvents())->setSelectedOption($processor->form_event_id)
			],
			[
				(new \Html\Form\Select($w))->setLabel('Processor')->setName('processor_class')->setOptions($processor_options)->setSelectedOption(((!empty($processor->class) && !empty($processor->module)) ? $processor->module . '.' . $processor->class : null))
			]
		]
	];
	$w->ctx('processor_form',Html::multiColForm($form,'/form-processor/edit/'.$processor->id . '?form_id=' . $form_id));
}

function edit_POST (Web $w) {
	$p = $w->pathMatch('id');
	if (empty($p['id'])) {
		$processor = new FormEventProcessor($w);
	} else {
		$processor = $w->Form->getEventProcessor($p['id']);
		if (empty($processor)) {
			$w->error('No processor found for id');
		}
	}
	$form_id = $w->request('form_id');
	if (empty($form_id)) {
		$w->error('No form id found');
	}

	$processor->name = $_POST['name'];
	$processor->form_event_id = $_POST['form_event_id'];
	$processor_class = $_POST['processor_class'];
	$processor_class = explode('.', $processor_class);
	$processor->module = $processor_class[0];
	$processor->class = $processor_class[1];
	$processor->insertOrUpdate();

	$w->msg('Processor Saved','/form/show/'. $form_id . '#events');

}