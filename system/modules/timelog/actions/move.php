<?php

function move_GET(Web $w)
{
    $p = $w->pathMatch("id");

    // get redirect, defaults to "/timelog"
    $redirect = Request::string("redirect", "/timelog");
    if ($redirect !== "/timelog") {
        $redirect = $redirect . "#timelog";
    }

    if (empty($p["id"])) {
        $w->out("No Timelog to move", $redirect);
        return;
    }

    $timelog = TimelogService::getInstance($w)->getTimelog($p["id"]);
    if (empty($timelog)) {
        $w->out("Timelog not found", $redirect);
        return;
    }

    if (!$timelog->canEdit(AuthService::getInstance($w)->user())) {
        $w->out("You cannot edit this Timelog", $redirect);
        return;
    }

    $w->ctx("timelog", $timelog);
    $w->ctx("redirect", $redirect);

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
            $validation["search"] = ["required"];
        }
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
            $form["Additional Fields"] = [];
            foreach ($additional_form_fields as $form_fields) {
                $form["Additional Fields"][] = $form_fields;
            }
        }
    }
    $w->ctx("form", $form);
}

function move_POST(Web $w)
{
    $p = $w->pathMatch("id");

    // get redirect, defaults to "/timelog"
    $redirect = Request::string("redirect", "/timelog");
    if ($redirect !== "/timelog") {
        $redirect = $redirect . "#timelog";
    }

    if (empty($p["id"])) {
        $w->error("No Timelog to move", $redirect);
        return;
    }

    $timelog = TimelogService::getInstance($w)->getTimelog($p["id"]);
    if (empty($timelog)) {
        $w->error("Timelog not found", $redirect);
        return;
    }

    if (!$timelog->canEdit(AuthService::getInstance($w)->user())) {
        $w->error("You cannot edit this Timelog", $redirect);
        return;
    }

    $timelog->object_class = Request::string("object_class");
    $timelog->object_id = Request::int("object_id");

    $timelog->update();

    $w->msg("<div id='saved_record_id' data-id='" . $timelog->id . "' >Timelog saved</div>", $redirect);
}
