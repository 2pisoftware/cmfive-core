<?php

function index_ALL(Web $w)
{
    $w->ctx("title", "Insights List");



    LogService::getInstance($w)->setLogger("INSIGHTS")->error("This is an INSIGHTS.INFO message");
    LogService::getInstance($w)->setLogger("INSIGHTS")->info("This is an INSIGHTS.INFO message");
    LogService::getInstance($w)->setLogger("INSIGHTS")->debug("This is an INSIGHTS.INFO message");
    LogService::getInstance($w)->setLogger("INSIGHTS")->warn("This is an INSIGHTS.INFO message");


    // access service functions using the Web $w object and the module name
    $insights = InsightService::getInstance($w)->getAllInsights('all');
    var_dump($insights);

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
