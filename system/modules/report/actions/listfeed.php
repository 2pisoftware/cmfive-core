<?php

// list available feeds in the feed dashboard
function listfeed_ALL(Web &$w) {
    ReportService::getInstance($w)->navigation($w, "Feeds");

    // get all feeds
    $feeds = ReportService::getInstance($w)->getFeeds();

    // prepare table headings
    $line = array(array("Feed", "Report", "Description", "Created", ""));

    // if feeds exists and i am suitably authorised, list them
    if (($feeds) && (AuthService::getInstance($w)->user()->hasRole("report_editor") || AuthService::getInstance($w)->user()->hasRole("report_admin"))) {
        foreach ($feeds as $feed) {
            // get report data
            $rep = ReportService::getInstance($w)->getReportInfo($feed->report_id);

            // display the details
            if ($rep) {
                $line[] = array(
                    $feed->title,
                    $rep->title,
                    $feed->description,
                    formatDateTime($feed->dt_created),
                    HtmlBootstrap5::b(WEBROOT . "/report/editfeed/" . $feed->id, " View ") .
                    HtmlBootstrap5::b(WEBROOT . "/report/deletefeed/" . $feed->id, " Delete ", "Are you sure you wish to DELETE this feed?")
                );
            }
        }
    } else {
        // no feeds and/or no access
        $line[] = array("No feeds to list", "", "", "", "");
    }

    // display results
    $w->ctx("feedlist", HtmlBootstrap5::table($line, null, "tablesorter", true));
}
