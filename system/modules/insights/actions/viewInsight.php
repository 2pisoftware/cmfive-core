<?php

function viewInsight_GET(Web $w) {
    //add a title to the action
    $w->ctx('title','View individual insight');

    // build the table array adding the headers and the row data
    $table = [];
    $tableHeaders = ['Name', 'Module', 'Description', 'Actions'];
    if (!empty($insights)) {
        foreach ($insights as $insights) {
            $row = [];
            // add values to the row in the same order as the table headers
            $row[] = $insights->name;
            $row[] = $insights->module;
            $row[] = $insights->description;
            // the actions column is used to hold buttons that link to actions per insight. Note the insight id is added to the href on these buttons.
            $actions = [];
            $row[] = implode('', $actions);
            $table[] = $row;
        }
    }

    //send the table to the template using ctx
    $w->ctx('insightTable', Html::table($table, 'insight_table', 'tablesorter', $tableHeaders));
}
?>    