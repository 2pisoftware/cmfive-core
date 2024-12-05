<?php
use Html\Form\Html5Autocomplete;
use Html\Form\InputField\Hidden;
use Html\Form\Select;

$usable_class = !empty($timelog->object_class) ? $timelog->object_class : (!empty($tracking_class) ? $tracking_class : (empty($select_indexes) ? null : $select_indexes[0][1]));
$where_clause = [];
if (!empty($usable_class)) {
    if (in_array('is_deleted', (new $usable_class($w))->getDbTableColumnNames())) {
        $where['is_deleted'] = 0;
    }
}

echo HtmlBootstrap5::multiColForm([
    "Move Timelog" => [
        [
            new Hidden([
                "id|name" => "object_id",
                "value" => $timelog->object_id ?: $tracking_id
            ]),

            new Select([
                "id|name" => "object_class",
                "label" => "Module",
                "options" => $select_indexes,
                "selected_option" => $timelog->object_class
                    ?: $tracking_class
                    ?: (empty($select_indexes) ? null : $select_indexes[0][1]),
            ]),

            new Html5Autocomplete([
                "id|name" => "search",
                "class" => "form-control",
                "label" => "Search",
                "title" => !empty($object) ? $object->getSelectOptionTitle() : null,
                "value" => !empty($timelog->object_id) ? $timelog->object_id : $tracking_id,
                "required" => "required",
                "source" => $w->localUrl("/timelog/ajaxSearch?index={$timelog->object_class}"),
                "options" => !empty($usable_class) ? TimelogService::getInstance($w)->getObjects($usable_class, $where) : '',
                "maxItems" => 1,
            ])
        ],
    ],

    ...$form,
], "/timelog/move/" . $timelog->id . ($redirect ? "?redirect=" . $redirect : ""));
