<?php

function start_GET(Web $w) {
    $indexes = SearchService::getInstance($w)->getIndexes();
    $select_indexes = [];
    if (!empty($indexes)) {
        foreach($indexes as $friendly_name => $search_name) {
            $select_indexes[] = array($friendly_name, $search_name);
        }
    }
    
    $form = [
        "Start Timelog" => [
            [["Module", "select", "module", null, $select_indexes]],
            [["Search", "text", "-search"]],
            [["object id", 'hidden', "object_id"]],
            [["Description", "text", "description"]]
        ]
    ];
    
    $w->ctx("form", HtmlBootstrap5::multiColForm($form, null, null, "Save", "timelogForm"));
}