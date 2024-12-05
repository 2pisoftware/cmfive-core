<?php
// Create Report: show fields in selected table to assist in Report creation
function taskAjaxSelectbyTable_ALL(Web $w) {
    $tbl = $_REQUEST['id'];

    // create dropdowns loaded with respective data
    $dbfields = ReportService::getInstance($w)->getFieldsinTable($tbl);

    $w->setLayout(null);
    $w->out(json_encode($dbfields));
}
