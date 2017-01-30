<?php

function edit_GET(Web $w) {
	
	$p = $w->pathMatch("id");
	
	$timelog = !empty($p['id']) ? $w->Timelog->getTimelog($p['id']) : new Timelog($w);
	$w->ctx("timelog", $timelog);
	$w->ctx('redirect', $w->request("redirect", ''));
	
        $indexes = $w->timelog->getLoggableObjects();
        $select_indexes = [];
        if (!empty($indexes)) {
            foreach($indexes as $friendly_name => $search_name) {
                
                $select_indexes[] = array($friendly_name, $search_name);
            }
        }
	$w->ctx("select_indexes", $select_indexes);

	$tracking_id = $w->request("id");
	$tracking_class = $w->request("class");
	$w->ctx("tracking_id", $tracking_id);
	$w->ctx("tracking_class", $tracking_class);
	
	// If timelog.object_id is required then we must require the search field
	$validation = Timelog::$_validation;
	if (!empty($validation["object_id"])) {
		if (in_array("required", $validation["object_id"])) {
			$validation["search"] = array('required');
		} 
	}
	
	$object = $w->Timelog->getObject($timelog->object_class ? : $tracking_class, $timelog->object_id ? : $tracking_id);
	$w->ctx("object", $object);
	// Hook relies on knowing the timelogs time_type record, but also the object, so we give the time_type to object
	if (!empty($object->id) && !empty($timelog->id)) {
		$object->time_type = $timelog->time_type;
	}
	
	$form = [];
	if (!empty($object)) {
		$additional_form_fields = $w->callHook("timelog", "type_options_for_" . get_class($object), $object);
		if (!empty($additional_form_fields[0])) {
			$form['Additional Fields'] = array();
			foreach($additional_form_fields as $form_fields) {
				$form['Additional Fields'][] = $form_fields;
			}
		}
	}
	$w->ctx("form", $form);
}

function edit_POST(Web $w) {
//	var_dump($_POST); die();
	$p = $w->pathMatch("id");
	
	$redirect = $w->request("redirect", '');
	
	$timelog = !empty($p['id']) ? $w->Timelog->getTimelog($p['id']) : new Timelog($w);

	// Get and save timelog
	if (empty($_POST['object_class']) || empty($_POST['object_id'])) {
		$w->error('Missing module or search data', $redirect ? : '/timelog');
	}
	
	if (!array_key_exists("date_start", $_POST) || !array_key_exists("time_start", $_POST) || (!$timelog->isRunning() && (!array_key_exists("time_end", $_POST) && !array_key_exists("hours_worked", $_POST)))) {
		$w->error('Missing date/time data', $redirect ? : '/timelog');
	}
	
	// Get start and end date/time
	$time_object = null;
	try {
		$time_object = new DateTime(str_replace('/', '-', $_POST['date_start']) . ' ' . $_POST['time_start']);
	} catch (Exception $e) {
		$w->Log->setLogger("TIMELOG")->error($e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
		$w->error('Invalid start date or time', $redirect ? : '/timelog');
	} 
	
	$timelog->object_class = $_POST['object_class'];
	$timelog->object_id = $_POST['object_id'];
	$timelog->time_type = !empty($_POST['time_type']) ? $_POST['time_type'] : null;
	
	$timelog->dt_start = $time_object->format('Y-m-d H:i:s');
	
	if ($_POST['select_end_method'] === "time") {
		try {
			$end_time_object = new DateTime(str_replace('/', '-', $_POST['date_start']) . ' ' . $_POST['time_end']);
			$timelog->dt_end = $end_time_object->format('Y-m-d H:i:s');
		} catch (Exception $e) {
			$w->Log->setLogger("TIMELOG")->error($e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
			$w->error('Invalid start date or time', $redirect ? : '/timelog');
		} 
	} else {
		if (!empty($_POST['hours_worked']) || !empty($_POST['minutes_worked'])) {
			$time_object->add(new DateInterval("PT" . intval($_POST['hours_worked']) . "H" . (!empty($_POST['minutes_worked']) ? intval($_POST['minutes_worked']) : 0) . "M0S"));
			$timelog->dt_end = $time_object->format('Y-m-d H:i:s');
		}
	}
	
	// Timelog user_id handled in insert/update
	$timelog->insertOrUpdate();
	
	// Save comment
	$timelog->setComment($_POST['description']);

	$w->msg("<div id='saved_record_id' data-id='".$timelog->id."' >Timelog saved</div>", (!empty($redirect) ? $redirect . "#timelog" : "/timelog"));
}
