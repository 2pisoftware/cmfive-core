<?php

use Html\Form\Select;
function createfeed_GET(Web &$w)
{
    ReportService::getInstance($w)->navigation($w, "Create a Feed");

    // get list of reports for logged in user. sort to list unapproved reports first
    $reports = ReportService::getInstance($w)->getReportsbyUserId($w->session('user_id'));

    // if i am a member of a list of reports, lets display them
    if (($reports) && (AuthService::getInstance($w)->user()->hasRole("report_editor")  || AuthService::getInstance($w)->user()->hasRole("report_admin"))) {
        foreach ($reports as $report) {
            // get report data
            $rep = ReportService::getInstance($w)->getReportInfo($report->report_id);
            $myrep[] = [$rep->title, $rep->id];
        }
    }

    $f = HtmlBootstrap5::multiColForm([
        "Create a Feed from a Report" => [
            [new Select(["id|name" => "rid", "label" => "Select Report", "options" => $myrep])]
        ]

        // ["Create a Feed from a Report", "section"],
        // ["Select Report", "select", "rid", null, $myrep],
        // ["Feed Title", "text", "title"],
        // ["Description", "textarea", "description", null, "40", "6"],
    ], $w->localUrl("/report/createfeed"), "POST", "Save");

    $w->ctx("createfeed", $f);
}

function createfeed_POST(Web &$w)
{
    ReportService::getInstance($w)->navigation($w, "Create a Feed");

    // create a new feed
    $feed = new ReportFeed($w);

    $arr["report_id"] = Request::int("rid");
    $arr["title"] = Request::string("title");
    $arr["description"] = Request::string("description");
    $arr["dt_created"] = date("d/m/Y");
    $arr["is_deleted"] = 0;

    $feed->fill($arr);
    $feed->report_key = uniqid();

    $rep = ReportService::getInstance($w)->getReportInfo($feed->report_id);

    // if report exists
    if ($rep) {
        // get the form array
        $elements = $rep->getReportCriteria();

        $query = "";
        if ($elements) {
            foreach ($elements as $element) {
                if (($element[0] != "Description") && (!empty($element[2]))) {
                    $query .= $element[2] . "=&lt;value&gt;&";
                }
            }
        }

        $query = rtrim($query, "&");
        $feedurl = $w->localUrl("/report/feed/?key=" . $feed->report_key . "&" . $query);

        $feed->url = $feedurl;
        $feed->insert(); // $feed->update();

        $feedurl = "<b>Your Feed has been created</b><p>Use the URL below, with actual query parameter values, to access this report feed.<p>" . $feedurl;
        $w->ctx("feedurl", $feedurl);
    }
}
