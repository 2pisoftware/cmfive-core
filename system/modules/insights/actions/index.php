<?php

function index_ALL(Web $w)
{
    $w->ctx("title", "Insights List");



    $w->Log->error("Insights this is a test");


    // access service functions using the Web $w object and the module name
    $insights = $w->Insights->getAllInsights('all');

    // build the table array adding the headers and the row data
    $table = [];
    $tableHeaders = ['Name'];
    if (!empty($insights)) {  // only loop if we have one or more reports
        foreach ($insights as $insight) { // loop through each report
            $row = [];
            // add values to the row in the same order as the table headers
            $row[] = $insight->name;
            // the actions column is used to hold buttons that link to actions per report. Note the report id is added to the href on these buttons.
            $actions = [];
            $row[] = implode('', $actions);
            $table[] = $row;
        }
    }

    //send the table to the template using ctx
    $w->ctx('insightsTable', Html::table($table, '#', '#', $tableHeaders));
}
