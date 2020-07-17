<?php

function edit_GET(Web $w)
{
    $p = $w->pathMatch('id');
    if (empty($p['id'])) {
        $event = new FormEvent($w);
    } else {
        $event = $w->Form->getFormEvent($p['id']);
        if (empty($event)) {
            $w->error('Could not find Event for id', '/form');
        }
    }
    $form_id = $w->request('form_id');
    if (empty($form_id)) {
        $w->error('No Form id found', '/form');
    }

    //retrieve form event processor classes from module configs
    $processor_options = $w->Form->getEventProcessorList();

    //setup application select options
    $applications = $w->Form->getFormApplicationsForFormId($form_id);
    $application_select_options = [['label' => '--- Select ---', 'value' => null]];
    foreach ($applications as $application) {
        $application_select_options[] = ['label' => $application->title, 'value' => $application->id];
    }

    //create edit form
    $form = [
        'Event' => [
            [
                (new \Html\Form\InputField($w))->setLabel('Title')->setName('title')->setValue($event->title)
            ],
            [
                (new \Html\Form\Select($w))->setLabel('Type')->setName('event_type')->setOptions($event->_event_type_ui_select_options)->setSelectedOption($event->event_type),
                (new \Html\Form\Inputfield\Checkbox($w))->setLabel('Active')->setName('is_active')->setChecked($event->is_active)
            ],
            [
                (new \Html\Form\Select($w))->setLabel('Form Application (Optional - Leave blank to add event to all form instances)')->setName('form_application_id')->setOptions($application_select_options)->setSelectedOption($event->form_application_id)
            ],
            [
                (new \Html\Form\Select($w))->setLabel('Processor')->setName('processor_class')->setOptions($processor_options)->setSelectedOption(((!empty($event->class) && !empty($event->module)) ? $event->module . '.' . $event->class : null))
            ]
        ]
    ];
    $w->ctx('event_form', Html::multiColForm($form, '/form-event/edit/' . $event->id . '?form_id=' . $form_id));
}

function edit_POST(Web $w)
{
    $p = $w->pathMatch('id');
    if (empty($p['id'])) {
        $event = new FormEvent($w);
    } else {
        $event = $w->Form->getFormEvent($p['id']);
        if (empty($event)) {
            $w->error('Could not find Event for id', '/form');
        }
    }
    $form_id = $w->request('form_id');
    if (empty($form_id)) {
        $w->error('No Form id found', '/form');
    }

    $event->fill($_POST);
    $event->is_active = isset($_POST['is_active']) ? 1 : 0;
    $event->form_id = $form_id;
    $processor_class = $_POST['processor_class'];
    $processor_class = explode('.', $processor_class);
    $event->module = $processor_class[0];
    $event->class = $processor_class[1];
    $event->insertOrUpdate();
    $w->msg('Form Event Saved', '/form/show/' . $event->form_id . '#events');
}
