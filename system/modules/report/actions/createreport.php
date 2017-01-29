<?php
//////////////////////////////////////////////////
//		CREATE REPORT			//
//////////////////////////////////////////////////

function createreport_ALL(Web &$w) {
    $w->Report->navigation($w, "Create a Report");

    // build form to create a report. display to users by role is controlled by the template
    // using lookup with type ReportCategory for category listing
    $f = Html::form(array(
        array("Create a New Report","section"),
        array("Title","text","title", $w->request('title')),
        array("Module","select","module", $w->request('module'), $w->Report->getModules()),
        // array("Category","select","category", $w->request('category'), lookupForSelect($w, "ReportCategory")),
        array("Description","textarea","description",$w->request('description'),"110","2"),
        array("Code","textarea","report_code",$w->request('report_code'),"110","22",false),
        array("Connection","select","report_connection_id",$w->request('report_connection_id'), $w->Report->getConnections())
    ), $w->localUrl("/report/savereport"), "POST", "Save Report");

    $t = Html::form(array(
        array("Special Parameters","section"),
        array("User","static","user","{{current_user_id}}"),
        array("Roles","static","roles","{{roles}}"),
        array("Site URL","static","webroot","{{webroot}}"),
        array("View Database","section"),
        array("Tables","select","dbtables",null,$w->Report->getAllDBTables()),
        array(" ","static","dbfields","<span id=dbfields></span>")
    ));

    // display form
    $w->ctx("createreport",$f);
    $w->ctx("dbform",$t);
}