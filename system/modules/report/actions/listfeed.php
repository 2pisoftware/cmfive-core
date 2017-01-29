<?php

// list available feeds in the feed dashboard
function listfeed_ALL(Web &$w) {
    $w->Report->navigation($w, "Feeds");

    // get all feeds
    $feeds = $w->Report->getFeeds();

    // prepare table headings
    $line = array(array("Feed", "Report", "Description", "Created", ""));

    // if feeds exists and i am suitably authorised, list them
    if (($feeds) && ($w->Auth->user()->hasRole("report_editor") || $w->Auth->user()->hasRole("report_admin"))) {
        foreach ($feeds as $feed) {
            // get report data
            $rep = $w->Report->getReportInfo($feed->report_id);

            // display the details
            if ($rep) {
                $line[] = array(
                    $feed->title,
                    $rep->title,
                    $feed->description,
                    formatDateTime($feed->dt_created),
                    Html::b(WEBROOT . "/report/editfeed/" . $feed->id, " View ") .
                    Html::b(WEBROOT . "/report/deletefeed/" . $feed->id, " Delete ", "Are you sure you wish to DELETE this feed?")
                );
            }
        }
    } else {
        // no feeds and/or no access
        $line[] = array("No feeds to list", "", "", "", "");
    }

    // display results
    $w->ctx("feedlist", Html::table($line, null, "tablesorter", true));
}
