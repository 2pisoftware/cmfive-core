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
    $redirect = Request::string("redirect", '');
    $w->ctx('redirect', $redirect);

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

    if (AuthService::getInstance($w)->user()->is_admin) {
        $options = AuthService::getInstance($w)->getUsers();
        usort($options, function ($a, $b) {
            return strcmp($a->getContact()->getFullName(), $b->getContact()->getFullName());
        });
    }

    // If timelog.object_id is required then we must require the search field
    $validation = Timelog::$_validation;
    if (!empty($validation["object_id"])) {
        if (in_array("required", $validation["object_id"])) {
            $validation["search"] = ['required'];
        }
    }

    $object = TimelogService::getInstance($w)->getObject($timelog->object_class ?: $tracking_class, $timelog->object_id ?: $tracking_id);
    $w->ctx("object", $object);
    // Hook relies on knowing the timelogs time_type record, but also the object, so we give the time_type to object
    if (!empty($object->id) && !empty($timelog->id)) {
        $object->time_type = $timelog->time_type;
    }

    //depending on permissions have a select or hidden field for selecting timelog user
    if (AuthService::getInstance($w)->user()->is_admin && !empty($options)) {
        $user_select = (new \Html\Form\Select([
            "id|name"    => "user_id",
            'required'  => true,
        ]))->setOptions($options, true)->setSelectedOption(empty($timelog->id) ? AuthService::getInstance($w)->user()->id : (!empty($timelog->user_id) ? $timelog->user_id : null))->setLabel('Assigned User');
    } else {
        $user_select = (new \Html\Form\InputField\Hidden([
            "id|name"        => "user_id",
            "value"        => empty($timelog->id) ? AuthService::getInstance($w)->user()->id : $timelog->user_id
        ]));
    }
    $form_header = (!empty($timelog->id)) ? "Update" : "Create";
    $form[$form_header] = [
        [
            $user_select,
        ],
        [
            (new Html\Form\Select([
                "id|name" => "object_class",
                "class" => "form-control",
                "selected_option" => $timelog->object_class ?: $tracking_class ?: (empty($select_indexes) ? null : $select_indexes[0][1]),
                "required" => true,
                "options" => $select_indexes,
            ]))->setLabel('Module'),


            (new Html\Cmfive\Autocomplete([
                "id|name" => "acp_search",
                "class" => "form-control",
                "required" => true,
                'url' => 'ajaxSearch?index=Task'
            ]))->setValueField('id')->setLabelField('value')->setSearchField('value')->setLabel('Search'),
        ],
        [
            (new \Html\Form\InputField\Date([
                "id|name"        => "date_start",
                "value"            => $timelog->getDateStart(),
                "required"        => true,
            ]))->setLabel('Date'),
            (new \Html\Form\InputField([
                "id|name"        => "time_start",
                "value"            => $timelog->getTimeStart(),
                "pattern"        => "^(0?[0-9]|1[0-9]|2[0-3]):[0-5][0-9](\s+)?(AM|PM|am|pm)?$",
                "placeholder"    => "12hr format: 11:30pm or 24hr format: 23:30",
                "required"        => "true"
            ]))->setLabel('Time Started'),
        ],
        (!$timelog->isRunning()) ? [
            (new \Html\Form\InputField\Radio([
                "name"        => "select_end_method",
                "value"        => "time",
                "class"        => "right",
                "style"        => "margin-top: 20px;",
                "checked"    => "true",
                "tabindex"    => -1
            ])),
            (new \Html\Form\InputField([
                "id|name"        => "time_end",
                "value"            => $timelog->getTimeEnd(),
                "pattern"        => "^(0?[0-9]|1[0-9]|2[0-3]):[0-5][0-9](\s+)?(AM|PM|am|pm)?$",
                "placeholder"    => "12hr format: 11:30pm or 24hr format: 23:30",
                "required"        => "true"
            ]))->setLabel('End Time'),
            (new \Html\Form\InputField\Radio([
                "name"        => "select_end_method",
                "value"        => "hours",
                "class"        => "right",
                "style"        => "margin-top: 20px;",
                "tabindex"    => -1
            ])),
            (new \Html\Form\InputField\Number([
                "id|name"        => "hours_worked",
                "value"            => $timelog->getHoursWorked(),
                "min"            => 0,
                "max"            => 23,
                "step"            => 1,
                "placeholder"    => "Hours: 0-23",
                "disabled"        => "true"
            ]))->setLabel('Hours Worked'),
            (new \Html\Form\InputField\Number([
                "id|name"        => "minutes_worked",
                "value"            => $timelog->getMinutesWorked(),
                "min"            => 0,
                "max"            => 59,
                "step"            => 1,
                "placeholder"    => "Mins: 0-59",
                "disabled"        => "true"
            ]))->setLabel('Minutes Worked'),
        ] : null,
        [
            (new \Html\Form\Textarea([
                "id|name"        => "description",
                "value"            => !empty($timelog->id) ? $timelog->getComment()->comment : null,
                "rows"            => 8
            ]))->setLabel('Description'),
        ],
        [
            (new \Html\Form\InputField(["type" => "hidden", "id|name" => "object_id", "value" => $timelog->object_id ?: $tracking_id])),
        ],
    ];
    if (!empty($object)) {
        $additional_form_fields = $w->callHook("timelog", "type_options_for_" . get_class($object), $object);
        if (!empty($additional_form_fields[0])) {
            foreach ($additional_form_fields as $form_fields) {
                $form[$form_header][] = $form_fields;
            }
        }
    }
    //method='POST' name='timelog_edit_form' target='_self' id='timelog_edit_form' class=' small-12 columns'>
    $w->ctx("form", HtmlBootstrap5::multiColForm($form, '/timelog/edit/' . (!empty($timelog->id) ? $timelog->id : '') . ($redirect ? '?redirect=' . $redirect : ''), "POST", "Save", 'timelog_edit_form', null, null, '_self', true, $validation));
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
            $timelog->dt_end = $end_time_object->format('Y-m-d H:i:s');
        } catch (Exception $e) {
            LogService::getInstance($w)->setLogger("TIMELOG")->error($e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
            $w->error('Invalid start date or time', $redirect ?: '/timelog');
        }
    } else {
        if (!empty($_POST['hours_worked']) || !empty($_POST['minutes_worked'])) {
            $time_object->add(new DateInterval("PT" . intval($_POST['hours_worked']) . "H" . (!empty($_POST['minutes_worked']) ? intval($_POST['minutes_worked']) : 0) . "M0S"));
            $timelog->dt_end = $time_object->format('Y-m-d H:i:s');
        }
    }

    if (empty($timelog->user_id)) {
        $timelog->user_id = !empty($_POST['user_id']) ? intval($_POST['user_id']) : AuthService::getInstance($w)->user()->id;
    }

    // Timelog user_id handled in insert/update
    $timelog->insertOrUpdate();

    // Save comment
    $timelog->setComment($_POST['description']);

    $w->msg("<div id='saved_record_id' data-id='" . $timelog->id . "' >Timelog saved</div>", (!empty($redirect) ? $redirect . "#timelog" : "/timelog"));
}
