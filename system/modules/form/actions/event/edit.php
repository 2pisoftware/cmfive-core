<?php

function edit_GET(Web $w)
{
    $p = $w->pathMatch('id');
    if (empty($p['id'])) {
        $event = new FormEvent($w);
    } else {
        $event = FormService::getInstance($w)->getFormEvent($p['id']);
        if (empty($event)) {
            $w->error('Could not find Event for id', '/form');
        }
    }
    $form_id = Request::int('form_id');
    if (empty($form_id)) {
        $w->error('No Form id found', '/form');
    }

    //retrieve form event processor classes from module configs
    $processor_options = FormService::getInstance($w)->getEventProcessorList();

    //setup application select options
    $applications = FormService::getInstance($w)->getFormApplicationsForFormId($form_id);
    $application_select_options = []; // [['label' => '--- Select ---', 'value' => null]];
    foreach ($applications as $application) {
        $application_select_options[] = ['label' => $application->title, 'value' => $application->id];
    }

    //create edit form
    $form = [
        'Event' => [
            [
                (new \Html\Form\InputField([
                    "label" => "Title",
                    "id|name" => "title",
                    "value" => $event->title,
                    "required" => true
                ]))
            ],
            [
                (new \Html\Form\Select([
                    "label" => "Type",
                    "id|name" => "event_type",
                    "required" => true
                ]))->setOptions($event->_event_type_ui_select_options)->setSelectedOption($event->event_type),
                (new \Html\Form\Inputfield\Checkbox([
                    "label" => "Active",
                    "id|name" => "is_active",
                    "required" => true
                ]))->setChecked($event->is_active)
            ],
            [
                (new \Html\Form\Select([
                    "label" => "Processor",
                    "id|name" => "processor_class",
                    "required" => true
                ]))->setOptions($processor_options)->setSelectedOption(((!empty($event->class) && !empty($event->module)) ? $event->module . '.' . $event->class : null))
            ],
            [
                (new \Html\Form\Select([
                    "label" => "Form Application (Optional - Leave blank to add event to all form instances)",
                    "id|name" => "form_application_id",
                ]))->setOptions($application_select_options)->setSelectedOption($event->form_application_id)
            ],
        ]
    ];
    $w->ctx('event_form', HtmlBootstrap5::multiColForm($form, '/form-event/edit/' . $event->id . '?form_id=' . $form_id));
}

function edit_POST(Web $w)
{
    $p = $w->pathMatch('id');
    if (empty($p['id'])) {
        $event = new FormEvent($w);
    } else {
        $event = FormService::getInstance($w)->getFormEvent($p['id']);
        if (empty($event)) {
            $w->error('Could not find Event for id', '/form');
        }
    }
    $form_id = Request::int('form_id');
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
    $event->form_application_id = Request::int('form_application_id');
    $event->insertOrUpdate();
    $w->msg('Form Event Saved', '/form/show/' . $event->form_id . '#events');
}
