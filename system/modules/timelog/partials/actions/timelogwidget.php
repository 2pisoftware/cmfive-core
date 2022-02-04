<?php namespace System\Modules\Timelog;

function timelogwidget(\Web $w) {
	
    $w->ctx("active_log", \TimelogService::getInstance($w)->getActiveTimelogForUser());
    $object = \TimelogService::getInstance($w)->hasTrackingObject() ? \TimelogService::getInstance($w)->getTrackingObject() : null;
    $w->ctx('tracked_object', $object);

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