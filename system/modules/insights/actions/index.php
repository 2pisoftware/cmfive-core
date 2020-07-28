<?php

function index_ALL(Web $w) {
    $w->ctx("title", "Insights List");  
}
// access service functions using the Web $w object and the module name
$Insights = $w->insight->getAllInsights();

// build the table array adding the headers and the row data
$table = [];
$tableHeaders = ['Name'];
if (!empty($Insight)) {  // only loop if we have one or more reports
    foreach ($Insight as $insights) { // loop through each report
        $row = [];
        // add values to the row in the same order as the table headers
        $row[] = $insights->name;
        // the actions column is used to hold buttons that link to actions per report. Note the report id is added to the href on these buttons.
        $actions = [];
        $row[] = implode('',$actions);
        $table[] = $row;
    }
}

//send the table to the template using ctx
$w->ctx('insightsTable', Html::table($table,'#','#',$tableHeaders));
}