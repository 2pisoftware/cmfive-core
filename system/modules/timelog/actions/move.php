<?php

function move_GET(Web $w)
{
    $w->setLayout('layout-bootstrap-5');
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


    $usable_class = !empty($timelog->object_class) ? $timelog->object_class : (!empty($tracking_class) ? $tracking_class : (empty($select_indexes) ? null : $select_indexes[0][1]));
    $where_clause = [];
    if (!empty($usable_class)) {
        if (in_array("is_deleted", (new $usable_class($w))->getDbTableColumnNames())) {
            $where_clause["is_deleted"] = 0;
        }
    }
    $acp_options = !empty($usable_class) ? TimelogService::getInstance($w)->getObjects($usable_class, $where_clause) : "";
    $w->ctx("acp_options", $acp_options);

    $form["Timelog"] = [
        [

            (new Html\Form\Select([
                "id|name" => "object_class",
                'title' => 'Module but better',
                "class" => "form-control",
                "selected_option" => $timelog->object_class ?: $tracking_class ?: (empty($select_indexes) ? null : $select_indexes[0][1]),
                "required" => true,
                "options" => $select_indexes,
                "data-value" => $timelog->object_class ?: $tracking_class ?: (empty($select_indexes) ? null : $select_indexes[0][1]),
            ]))->setLabel('Module'),


            (new Html\Cmfive\Autocomplete([
                "id|name" => "acp_search",
                "class" => "form-control",
                // "title" => !empty($object) ? $object->getSelectOptionTitle() : null,
                "required" => true,
                'url' => 'ajaxSearch?index=Task'
            ]))->setLabel('Search'),

        ],
        [
            (new \Html\Form\InputField(["type" => "hidden", "id|name" => "object_id", "value" => $timelog->object_id ?: $tracking_id])),
        ],
    ];

    if (!empty($object)) {
        $additional_form_fields = $w->callHook("timelog", "type_options_for_" . get_class($object), $object);
        if (!empty($additional_form_fields[0])) {
            foreach ($additional_form_fields as $additional_field) {
                $form['Timelog'][] = $additional_field;
            }
        }
    }


    // (new \Html\Form\InputField(["type" => "hidden", "id|name" => "object_id", "value" => $timelog->object_id ?: $tracking_id]));

    $w->ctx('form', HtmlBootstrap5::multiColForm($form, "/timelog/move/{$timelog->id}", "POST", "Save", "timelogform"));
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
