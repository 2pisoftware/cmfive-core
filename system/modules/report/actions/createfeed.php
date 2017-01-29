<?php
function createfeed_GET(Web &$w) {
    $w->Report->navigation($w, "Create a Feed");

    // get list of reports for logged in user. sort to list unapproved reports first
    $reports = $w->Report->getReportsbyUserId($w->session('user_id'));

    // if i am a member of a list of reports, lets display them
    if (($reports) && ($w->Auth->user()->hasRole("report_editor")  || $w->Auth->user()->hasRole("report_admin"))) {
        foreach ($reports as $report) {
            // get report data
            $rep = $w->Report->getReportInfo($report->report_id);
            $myrep[] = array($rep->title, $rep->id);
        }
    }

    $f = Html::form(array(
        array("Create a Feed from a Report", "section"),
        array("Select Report", "select", "rid", null, $myrep),
        array("Feed Title", "text", "title"),
        array("Description", "textarea", "description", null, "40", "6"),
    ), $w->localUrl("/report/createfeed"), "POST", "Save");

    $w->ctx("createfeed",$f);
}

function createfeed_POST(Web &$w) {
    $w->Report->navigation($w, "Create a Feed");

    // create a new feed
    $feed = new ReportFeed($w);

    $arr["report_id"] = $w->request("rid");
    $arr["title"] = $w->request("title");
    $arr["description"] = $w->request("description");
    $arr["dt_created"] = date("d/m/Y");
    $arr["is_deleted"] = 0;
    
    $feed->fill($arr);
    $feed->report_key = uniqid();
    
    $rep = $w->Report->getReportInfo($feed->report_id);

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

        $query = rtrim($query,"&");
        $feedurl = $w->localUrl("/report/feed/?key=" . $feed->report_key . "&" . $query);

        $feed->url = $feedurl;
        $feed->insert(); // $feed->update();

        $feedurl = "<b>Your Feed has been created</b><p>Use the URL below, with actual query parameter values, to access this report feed.<p>" . $feedurl;
        $w->ctx("feedurl", $feedurl);
    }
}