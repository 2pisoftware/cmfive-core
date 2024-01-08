<?php

function show_GET(Web $w)
{
    $w->setLayout('layout-bootstrap-5');
    
    $p = $w->pathMatch("id");
    if (empty($p['id'])) {
        $w->error("Form not found", "/form");
    }

    $w->enqueueStyle(["uri" => "/system/modules/form/assets/css/form-style.css", "weight" => 500, "name" => "form-style"]);

    VueComponentRegister::registerComponent('metadata-subform', new VueComponent('metadata-subform', '/system/modules/form/assets/js/metadata-subform.vue.js'));
    VueComponentRegister::registerComponent('metadata-select', new VueComponent('metadata-select', '/system/modules/form/assets/js/metadata-select.vue.js', '/system/modules/form/assets/js/metadata-select.vue.css'));

    $_form_object = FormService::getInstance($w)->getForm($p['id']);

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
            $row = [];
            $row[] = $event->title;
            $row[] = $event->event_type;
            $row[] = $event->is_active ? 'ON' : 'OFF';
            $row[] = !empty($event->form_application_id) ? FormService::getInstance($w)->getFormApplication($event->form_application_id)->title : 'None';
            $row[] = $event->module . '.' . $event->class;
            // add settings
            $row[] = str_replace(',', ',<br>', $event->settings);
            $actions = [];
            $actions[] = Html::box('/form-event/edit/' . $event->id . '?form_id=' . $_form_object->id, 'Edit', true);
            $actions[] = Html::b('/form-event/delete/' . $event->id, 'Delete', 'Are you sure you want to delete this event?', null, false, "alert");
            $actions[] = Html::box('/form-event/settings/' . $event->id . '?form_id=' . $_form_object->id, 'Edit Settings', true);
            $row[] = implode('', $actions);
            $event_table[] = $row;
        }
        $w->ctx('event_table', Html::table($event_table, null, 'tablesorter', $event_table_headers));
    }
}
