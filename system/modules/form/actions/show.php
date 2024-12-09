<?php

function show_GET(Web $w)
{
    list($form_id) = $w->pathMatch();
    if (empty($form_id)) {
        $w->error("Form not found", "/form");
    }
    
    CmfiveScriptComponentRegister::registerComponent("editField", new CmfiveScriptComponent("/system/templates/base/dist/editField.js", ["weight" => "200", "type" => "module"]));

    $_form_object = FormService::getInstance($w)->getForm($form_id);

    $w->ctx("title", "Form: " . $_form_object->printSearchTitle());
    $w->ctx("form", $_form_object);
    $w->ctx("fields", $_form_object->getFields());
    $events = $_form_object->getFormEvents();
    $processors = [];
    if (!empty($events)) {
        //prepare events table
        $event_table_headers = ['Name', 'Type', 'ON/Off', 'Application', 'Processor', 'Settings', 'Actions'];
        $event_table = [];
        foreach ($events as $event) {
            $event_table[] = [
                $event->title,
                $event->event_type,
                $event->is_active ? 'ON' : 'OFF',
                !empty($event->form_application_id) ? FormService::getInstance($w)->getFormApplication($event->form_application_id)->title : 'None',
                $event->module . '.' . $event->class,
                // add settings
                str_replace(',', ',<br>', $event->settings),
                HtmlBootstrap5::buttonGroup(
                    HtmlBootstrap5::box(
                        href: '/form-event/edit/' . $event->id . '?form_id=' . $_form_object->id,
                        title: 'Edit',
                        class: 'btn btn-sm btn-secondary',
                        button: true,
                    ) .
                    HtmlBootstrap5::b(
                        href: '/form-event/delete/' . $event->id,
                        title: 'Delete',
                        confirm: 'Are you sure you want to delete this event?',
                        class: 'btn btn-sm btn-danger',
                    ) .
                    HtmlBootstrap5::box(
                        href: '/form-event/settings/' . $event->id . '?form_id=' . $_form_object->id,
                        title: 'Edit Settings',
                        class: 'btn btn-sm btn-info',
                        button: true,
                    )
                )
            ];
        }
        $w->ctx('event_table', HtmlBootstrap5::table($event_table, null, 'tablesorter', $event_table_headers));
    }
}
