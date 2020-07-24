<?php

function index_ALL(Web $w) {
    $w->ctx("title", "Reports List");  
}
// access service functions using the Web $w object and the module name
$InsightsReports = $w->insights->getAllReports();

// build the table array adding the headers and the row data
$table = [];
$tableHeaders = ['Name'];
if (!empty($InsightsReports)) {  // only loop if we have one or more reports
    foreach ($InsightsReports as $reports) { // loop through each report
        $row = [];
        // add values to the row in the same order as the table headers
        $row[] = $reports->name;
        // the actions column is used to hold buttons that link to actions per report. Note the report id is added to the href on these buttons.
        $actions = [];
        $row[] = implode('',$actions);
        $table[] = $row;
    }
}

//send the table to the template using ctx
$w->ctx('reportsTable', Html::table($table,'#','#',$tableHeaders));
}