<?php

use Html\Form\Html5Autocomplete;
use Html\Form\InputField;
use Html\Form\InputField\Radio;
use Html\Form\Select;
use Html\Form\Textarea;

$allow_assigning = AuthService::getInstance($w)->user()->is_admin && !empty($options);

$usable_class = !empty($timelog->object_class) ? $timelog->object_class : (!empty($tracking_class) ? $tracking_class : (empty($select_indexes) ? null : $select_indexes[0][1]));
$where_clause = [];
if (!empty($usable_class))
{
    if (in_array('is_deleted', (new $usable_class($w))->getDbTableColumnNames()))
    {
        $where['is_deleted'] = 0;
    }
}

echo HtmlBootstrap5::multiColForm([
    ((!empty($timelog->id)) ? "Update" : "Create") . " timelog" => [
        [
            new InputField\Hidden([
                "id|name" => "object_id",
                "value" => $timelog->object_id ?: $tracking_id
            ]),

            $allow_assigning
                ? (new Select([
                    "label" => "Assigned User",
                    "required" => "required",
                    "id|name" => "user_id",
                    "options" => $options,
                    "value" => empty($timelog->id)
                        ? AuthService::getInstance($w)->user()->id
                        : (!empty($timelog->user_id) ? $timelog->user_id : null)
                ])) : (new InputField\Hidden([
                    "id|name" => "user_id",
                    "value" => empty($timelog->id) ? AuthService::getInstance($w)->user()->id : $timelog->user_id,
                ])),
        ],

        [
            (new Select([
                "label" => "Module",
                "id|name" => "object_class",
                "options" => $select_indexes,
                "selected_option" => $timelog->object_class
                    ?: $tracking_class
                    ?: (empty($select_indexes) ? null : $select_indexes[0][1]),
            ])),

            (new Html5Autocomplete([
                "label" => "Search",
                "id|name" => "search",
                "title" => !empty($object) ? $object->getSelectOptionTitle() : null,
                "value" => !empty($timelog->object_id) ? $timelog->object_id : $tracking_id,
                "required" => "required",
                "source" => $w->localUrl("/timelog/ajaxSearch?index={$timelog->object_class}"),
                "options" => !empty($usable_class) ? TimelogService::getInstance($w)->getObjects($usable_class, $where) : '',
            ]))
        ],

        [
            (new InputField\Date([
                "label" => "Date",
                "id|name" => "date_start",
                "value" => $timelog->getDateStart(),
                "required" => "required"
            ])),

            (new InputField([
                "label" => "Time Started",
                "id|name" => "time_start",
                "value" => $timelog->getTimeStart(),
                "required" => "required",
                "pattern"        => "^(0?[0-9]|1[0-9]|2[0-3]):[0-5][0-9](\s+)?(AM|PM|am|pm)?$",
                "placeholder"    => "12hr format: 11:30pm or 24hr format: 23:30",
            ]))
        ],

        [
            new Radio([
                "name"        => "select_end_method",
                "value"        => "time",
                "class"        => "right",
                "checked"    => "true",
            ]),
            new InputField([
                "label" => "End Time",
                "id|name"        => "time_end",
                "value"            => $timelog->getTimeEnd(),
                "pattern"        => "^(0?[0-9]|1[0-9]|2[0-3]):[0-5][0-9](\s+)?(AM|PM|am|pm)?$",
                "placeholder"    => "12hr format: 11:30pm or 24hr format: 23:30",
                "required"        => "true"
            ]),

            new Radio([
                "name"        => "select_end_method",
                "value"        => "hours",
                "class"        => "right",
                "checked"    => "true",
            ]),
            new InputField\Number([
                "label" => "Hours worked",
                "id|name"        => "hours_worked",
                "value"            => $timelog->getHoursWorked(),
                "min"            => 0,
                "max"            => 23,
                "step"            => 1,
                "placeholder"    => "Hours: 0-23",
                "disabled"        => "true"
            ]),
            new InputField\Number([
                "label" => "Minutes worked",
                "id|name"        => "minutes_worked",
                "value"            => $timelog->getMinutesWorked(),
                "min"            => 0,
                "max"            => 59,
                "step"            => 1,
                "placeholder"    => "Mins: 0-59",
                "disabled"        => "true"
            ])
        ],

        [
            new Textarea([
                "label" => "Description",
                "id|name"        => "description",
                "value"            => !empty($timelog->id) ? $timelog->getComment()->comment : null,
                "rows"            => 8
            ])
        ]
    ]
]);
