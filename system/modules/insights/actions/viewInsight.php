<?php

function viewInsight_GET(Web $w) {
    
    // now we need to fetch the correct insight
    // we will use pathMatch to retrieve an insight id from the url.
    $p = $w->pathMatch('id');
    // if the id exists we will retrieve the data for that insight otherwise we will return an error. 
    $insights = !empty($p['id']) ? InsightSerive::getInstance($w)->getInsightForId($p['id']);

    //add a title to the action
    // change the title to reflect viewing insight
    $w->ctx('title', !empty($p['id']) ? 'View Insight';

    // build the table array adding the headers and the row data
    $table = [];
    $tableHeaders = ['Name', 'Module', 'Description', 'Actions'];
    // We now need to change the value for each column to reflect the values of the insight we are viewing only. 
    if (!empty($p['id'])) {
        $getUrl = '/insights/viewInsight/' . $insights->id;
    } 
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