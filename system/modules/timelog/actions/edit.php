<?php

function edit_GET(Web $w)
{
    $p = $w->pathMatch("id");

    if (!empty($p['id'])) {
        $timelog = TimelogService::getInstance($w)->getTimelog($p['id']);
        if (empty($timelog)) {
            $w->msg("Timelog not found", "/timelog");
        }
        if (!$timelog->canEdit(AuthService::getInstance($w)->user())) {
            $w->msg("You cannot edit this Timelog", "/timelog");
        }
    } else {
        $timelog = new Timelog($w);
    }

    $w->ctx("timelog", $timelog);
    $w->ctx('redirect', Request::string("redirect", ''));

    $indexes = TimelogService::getInstance($w)->getLoggableObjects();
    $select_indexes = [];
    if (!empty($indexes)) {
        foreach ($indexes as $friendly_name => $search_name) {
            $select_indexes[] = [$friendly_name, $search_name];
        }
    }
    $w->ctx("select_indexes", $select_indexes);

    $tracking_id = Request::int("id");
    $tracking_class = Request::string("class");
    $w->ctx("tracking_id", $tracking_id);
    $w->ctx("tracking_class", $tracking_class);

    // If timelog.object_id is required then we must require the search field
    $validation = Timelog::$_validation;
    if (!empty($validation["object_id"])) {
        if (in_array("required", $validation["object_id"])) {
            $validation["search"] = ['required'];
        }
    }

    if (!empty($timelog->object_class) && !class_exists($timelog->object_class)) {
        $w->out(HtmlBootstrap5::alertBox("Invalid Timelog object class", "alert-danger", false));
        return;
    }

    $object = TimelogService::getInstance($w)->getObject($timelog->object_class ?: $tracking_class, $timelog->object_id ?: $tracking_id);
    $w->ctx("object", $object);
    // Hook relies on knowing the timelogs time_type record, but also the object, so we give the time_type to object
    if (!empty($object->id) && !empty($timelog->id)) {
        $object->time_type = $timelog->time_type;
    }

    $form = [];
    if (!empty($object)) {
        $additional_form_fields = $w->callHook("timelog", "type_options_for_" . get_class($object), $object);
        if (!empty($additional_form_fields[0])) {
            $form['Additional Fields'] = [];
            foreach ($additional_form_fields as $form_fields) {
                $form['Additional Fields'][] = $form_fields;
            }
        }
    }
    $w->ctx("form", $form);

    if (AuthService::getInstance($w)->user()->is_admin) {
        $users = AuthService::getInstance($w)->getUsers();
        usort($users, function ($a, $b) {
            return strcmp($a->getContact()->getFullName(), $b->getContact()->getFullName());
        });
        $w->ctx("options", $users);
    }
}

function edit_POST(Web $w)
{
    $p = $w->pathMatch("id");
    $redirect = Request::string("redirect", '');

    $timelog = !empty($p['id']) ? TimelogService::getInstance($w)->getTimelog($p['id']) : new Timelog($w);

    // Get and save timelog
    if (empty($_POST['object_class']) || empty($_POST['object_id'])) {
        $w->error('Missing module or search data', $redirect ?: '/timelog');
    }

    if (!array_key_exists("date_start", $_POST) || !array_key_exists("time_start", $_POST) || (!$timelog->isRunning() && (!array_key_exists("time_end", $_POST) && !array_key_exists("hours_worked", $_POST)))) {
        $w->error('Missing date/time data', $redirect ?: '/timelog');
    }

    // Get start and end date/time
    $time_object = null;
    try {
        $time_object = new DateTime(str_replace('/', '-', $_POST['date_start']) . ' ' . $_POST['time_start']);
    } catch (Exception $e) {
        LogService::getInstance($w)->setLogger("TIMELOG")->error($e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
        $w->error('Invalid start date or time', $redirect ?: '/timelog');
    }

    $timelog->object_class = $_POST['object_class'];
    $timelog->object_id = $_POST['object_id'];
    $timelog->time_type = !empty($_POST['time_type']) ? $_POST['time_type'] : null;

    $timelog->dt_start = $time_object->format('Y-m-d H:i:s');

    if ($_POST['select_end_method'] === "time") {
        try {
            $end_time_object = new DateTime(str_replace('/', '-', $_POST['date_start']) . ' ' . $_POST['time_end']);
            if ($end_time_object < $time_object) {
                $w->error("End time cannot be before start time.", $redirect ?: '/timelog');
            }

            $timelog->dt_end = $end_time_object->format('Y-m-d H:i:s');
        } catch (Exception $e) {
            LogService::getInstance($w)->setLogger("TIMELOG")->error($e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
            $w->error('Invalid start date or time', $redirect ?: '/timelog');
        }
    } else {
        if (!empty($_POST['hours_worked']) || !empty($_POST['minutes_worked'])) {
            $end_time_object = $time_object;
            $end_time_object->add(new DateInterval("PT" . intval($_POST['hours_worked']) . "H" . (!empty($_POST['minutes_worked']) ? intval($_POST['minutes_worked']) : 0) . "M0S"));
            if ($end_time_object < $time_object) {
                $w->error("End time cannot be before start time.", $redirect ?: '/timelog');
            }

            $timelog->dt_end = $time_object->format('Y-m-d H:i:s');
        }
    }

    if (empty($timelog->user_id)) {
        $timelog->user_id = !empty($_POST['user_id']) ? intval($_POST['user_id']) : AuthService::getInstance($w)->user()->id;
    }

    // Check to see if timelog with same starting time already exists, and remove if duplicate
    $timelogs_for_task = TimelogService::getInstance($w)->getTimelogsForObject($timelog->getLinkedObject());
    $timelogs_for_task_and_user = [];
    foreach ($timelogs_for_task as $existing_timelog) {
        if ($existing_timelog->user_id == $timelog->user_id) {
            $timelogs_for_task_and_user[] = $existing_timelog;
        }
    }

    foreach ($timelogs_for_task_and_user as $existing_timelog_for_task_and_user) {
        if (gmdate('Y-m-d', strtotime($existing_timelog_for_task_and_user->getDateStart() . ' ' . $existing_timelog_for_task_and_user->getTimeStart())) == substr($timelog->dt_start, 0, 10) && gmdate('H:i', strtotime($existing_timelog_for_task_and_user->getTimeStart())) == substr($timelog->dt_start, 11, 5)) {
            $w->error('Duplicate Timelog Removed', $redirect ?: '/timelog');
        }
    }

    // Timelog user_id handled in insert/update
    $timelog->insertOrUpdate();

    // Save comment
    $timelog->setComment($_POST['description']);

    $w->msg("<div id='saved_record_id' data-id='" . $timelog->id . "' >Timelog saved</div>", (!empty($redirect) ? $redirect . "#timelog" : "/timelog"));
}
