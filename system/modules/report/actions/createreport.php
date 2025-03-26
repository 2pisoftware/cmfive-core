<?php

use Html\Cmfive\QuillEditor;
use Html\Form\InputField;
use Html\Form\Select;
use Html\Form\Textarea;
//////////////////////////////////////////////////
//		CREATE REPORT			//
//////////////////////////////////////////////////

function createreport_ALL(Web &$w)
{
    ReportService::getInstance($w)->navigation($w, "Create a Report");

    // build form to create a report. display to users by role is controlled by the template
    // using lookup with type ReportCategory for category listing
    $f = HtmlBootstrap5::multiColForm([
        "Create a new Report" => [
            [new InputField([
                "id|name" => "title",
                "label" => "Title",
                "value" => Request::string("title"),
            ])],

            [new Select([
                "id|name" => "module",
                "label" => "Module",
                "selected_option" => Request::string("module"),
                "options" => ReportService::getInstance($w)->getModules(),
            ])],

            // [new Select([
            //     "id|name" => "category",
            //     "label" => "Category",
            //     "value" => Request::string("module"),
            //     "options" => lookupForSelect($w, "ReportCategory"),
            // ])]

            [new QuillEditor([
                "id|name" => "description",
                "label" => "Description",
                "value" => Request::string("description"),
            ])],

            [new Textarea([
                "id|name" => "report_code",
                "label" => "Code",
                "value" => Request::string("report_code"),
                "rows" => 22,
                "cols" => 110
            ])],

            [new Select([
                "id|name" => "report_connection_id",
                "label" => "Connect",
                "selected_option" => Request::int("report_connection_id"),
                "options" => ReportService::getInstance($w)->getConnections(),
            ])]
        ]
    ], $w->localUrl("/report/savereport"), "POST", "Save Report");

    // $t = HtmlBootstrap5::form(array(
    //     array("Special Parameters", "section"),
    //     array("User", "static", "user", "{{current_user_id}}"),
    //     array("Roles", "static", "roles", "{{roles}}"),
    //     array("Site URL", "static", "webroot", "{{webroot}}"),
    //     array("View Database", "section"),
    //     array("Tables", "select", "dbtables", null, ReportService::getInstance($w)->getAllDBTables()),
    //     array(" ", "static", "dbfields", "<span id=dbfields></span>")
    // ));

    // display form
    $w->ctx("createreport", $f);
    $w->ctx("dbform", $t);
}
