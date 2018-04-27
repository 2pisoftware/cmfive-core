<?php

function show_GET(Web $w) {
	
	$p = $w->pathMatch("id");
	if (empty($p['id'])) {
		$w->error("Form not found", "/form");
	}
	
	$w->enqueueStyle(["uri" => "/system/modules/form/assets/css/form-style.css", "weight" => 500]);
	
	VueComponentRegister::registerComponent('metadata-subform', new VueComponent('metadata-subform', '/system/modules/form/assets/js/metadata-subform.vue.js'));
	VueComponentRegister::registerComponent('metadata-select', new VueComponent('metadata-select', '/system/modules/form/assets/js/metadata-select.vue.js', '/system/modules/form/assets/js/metadata-select.vue.css'));
	
	$_form_object = $w->Form->getForm($p['id']);
	
	$w->ctx("title", "Form: " . $_form_object->printSearchTitle());
	$w->ctx("form", $_form_object);
	$w->ctx("fields", $_form_object->getFields());
	$events = $_form_object->getEvents();
	$processors = [];
	if (!empty($events)) {
		//prepare events table
		$event_table_headers = ['Name','Type','Description','ON/Off','Actions'];
		$event_table = [];
		foreach ($events as $event) {
			$row = [];
			$row[] = $event->title;
			$row[] = $event->type;
			$row[] = $event->description;
			$row[] = $event->is_active ? 'ON' : 'OFF';
			$actions = [];
			$actions[] = Html::box('/form-event/edit/' . $event->id . '?form_id=' . $_form_object->id, 'Edit', true);
			$actions[] = Html::b('/form-event/delete/' . $event->id, 'Delete', 'Are you sure you want to delete this event?', null, false, "alert");
			$row[] = implode('', $actions);
			$event_table[] = $row;
			//get processors for event
			$event_processors = $event->getEventProcessors();
			if (is_array($event_processors)) {
				$processors = array_merge($processors,$event_processors);
			} elseif (!empty($event_processors)) {
				$processors[] = $event_processors;
			}
			
		} 
		$w->ctx('event_table',Html::table($event_table, null, 'tablesorter', $event_table_headers));
	}
	// echo "<pre>";
	// var_dump($processors);
	if (!empty($processors)) {
		$processors_table_headers = ['Name','Event','Module','Class','Actions'];
		$processors_table = [];
		foreach ($processors as $processor) {
			$row = [];
			$row[] = $processor->name;
			$row[] = $w->Form->getFormEvent($processor->form_event_id)->title;
			$row[] = $processor->module;
			$row[] = $processor->class;
			$actions = [];
			$actions[] = Html::box('/form-processor/edit/' . $processor->id . '?form_id=' . $_form_object->id, 'Edit',true);
			$actions[] = Html::b('/form-processor/delete/' . $processor->id . '?form_id=' . $_form_object->id, 'Delete','Do you really want to delete this processor?', null, false, 'alert');
			$actions[] = Html::box('/form-processor/settings/' . $processor->id . '?form_id=' . $_form_object->id, 'Edit Settings', true);
			$row[] = implode(' ', $actions);
			$processors_table[] = $row;
		}
		$w->ctx('processors_table', Html::table($processors_table,null, 'tablesorter',$processors_table_headers));
	}
}