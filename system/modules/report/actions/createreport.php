<?php
//////////////////////////////////////////////////
//		CREATE REPORT			//
//////////////////////////////////////////////////

function createreport_ALL(Web &$w) {
    $w->Report->navigation($w, __("Create a Report"));

    // build form to create a report. display to users by role is controlled by the template
    // using lookup with type ReportCategory for category listing
    $f = Html::form(array(
        array(__("Create a New Report"),"section"),
        array(__("Title"),"text","title", $w->request('title')),
        array(__("Module"),"select","module", $w->request('module'), $w->Report->getModules()),
        // array("Category","select","category", $w->request('category'), lookupForSelect($w, "ReportCategory")),
        array(__("Description"),"textarea","description",$w->request('description'),"110","2"),
        array(__("Code"),"textarea","report_code",$w->request('report_code'),"110","22",false),
        array(__("Connection"),"select","report_connection_id",$w->request('report_connection_id'), $w->Report->getConnections())
    ), $w->localUrl("/report/savereport"), "POST", __("Save Report"));

    $t = Html::form(array(
        array(__("Special Parameters"),"section"),
        array(__("User"),"static","user","{{current_user_id}}"),
        array(__("Roles"),"static","roles","{{roles}}"),
        array(__("Site URL"),"static","webroot","{{webroot}}"),
        array(__("View Database"),"section"),
        array(__("Tables"),"select","dbtables",null,$w->Report->getAllDBTables()),
        array(" ","static","dbfields","<span id=dbfields></span>")
    ));

    // display form
    $w->ctx("createreport",$f);
    $w->ctx("dbform",$t);
}
