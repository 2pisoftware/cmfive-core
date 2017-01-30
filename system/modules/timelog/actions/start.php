<?php

function start_GET(Web $w) {
    $indexes = $w->search->getIndexes();
    $select_indexes = [];
    if (!empty($indexes)) {
        foreach($indexes as $friendly_name => $search_name) {
            $select_indexes[] = array($friendly_name, $search_name);
        }
    }
    
    $form = [
        __("Start Timelog") => [
            [[__("Module"), "select", "module", null, $select_indexes]],
            [[__("Search"), "text", "-search"]],
            [["object id", 'hidden', "object_id"]],
            [[__("Description"), "text", "description"]]
        ]
    ];
    
    $w->ctx("form", Html::multiColForm($form, null, null, __("Save"), "timelogForm"));
}
