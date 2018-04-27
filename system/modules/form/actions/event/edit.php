<?php

function edit_GET (Web $w) {
	$p = $w->pathMatch('id');
	if (empty($p['id'])) {
		$event = new FormEvent($w);
	} else {
		$event = $w->Form->getFormEvent($p['id']);
		if (empty($event)) {
			$w->error('Could not find Event for id','/form');
		}
	}
	$form_id = $w->request('form_id');
	if (empty($form_id)) {
		$w->error('No Form id found','/form');
	}
	//create edit form
	$form = [
		'Event' => [
			[
				(new \Html\Form\Inputfield($w))->setLabel('Title')->setName('title')->setValue($event->title)
			],
			[
				(new \Html\Form\Select($w))->setLabel('Type')->setName('type')->setOptions($event->_type_ui_select_options)->setSelectedOption($event->type),
				(new \Html\Form\Inputfield\Checkbox($w))->setLabel('Active')->setName('is_active')->setChecked($event->is_active)
			],
			[
				(new \Html\Form\Textarea($w))->setLabel('Description')->setName('description')->setValue($event->description)
			]
		]
	];
	$w->ctx('event_form',Html::multiColForm($form,'/form-event/edit/'.$event->id . '?form_id=' . $form_id));
}

function edit_POST (Web $w) {
	$p = $w->pathMatch('id');
	if (empty($p['id'])) {
		$event = new FormEvent($w);
	} else {
		$event = $w->Form->getFormEvent($p['id']);
		if (empty($event)) {
			$w->error('Could not find Event for id','/form');
		}
	}
	$form_id = $w->request('form_id');
	if (empty($form_id)) {
		$w->error('No Form id found','/form');
	}

	$event->fill($_POST);
	$event->is_active = isset($_POST['is_active']) ? 1 : 0;
	$event->form_id = $form_id;
	$event->insertOrUpdate();
	$w->msg('Form Event Saved','/form/show/' . $event->form_id . '#events');
}