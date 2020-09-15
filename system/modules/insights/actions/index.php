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
        foreach ($insights as $insight) {
            $row = [];
            // add values to the row in the same order as the table headers
            $row[] = $insight->name;
            $row[] = $insight->module;
            $row[] = $insight->description;
            // the actions column is used to hold buttons that link to actions per insight. Note the insight id is added to the href on these buttons.
            $actions = [];
            $actions[] = Html::b('/insights/viewInsight/' . $insight->id,'View this insight');
            $row[] = implode('', $actions);
            $table[] = $row;
        }
    }

    //send the table to the template using ctx
    $w->ctx('insightTable', Html::table($table, 'insight_table', 'tablesorter', $tableHeaders));
}
