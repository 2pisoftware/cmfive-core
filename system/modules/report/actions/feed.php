<?php
// receive, process and deliver feeds
function feed_ALL(Web &$w)
{
    // check for feed key in request
    if (array_key_exists("key", $_REQUEST)) {
        // get feed
        $feed = ReportService::getInstance($w)->getFeedInfobyKey($_REQUEST["key"]);

        // if feed, then get respective report details
        if ($feed) {
            $rep = ReportService::getInstance($w)->getReportInfo($feed->report_id);

            // if report exists, execute it
            if ($rep) {
                ReportService::getInstance($w)->navigation($w, $rep->title);

                // prepare and execute the report
                $tbl = $rep->getReportData();

                // if we have an empty return, say as much
                if (!$tbl) {
                    // return error status?
                } elseif ($tbl[0][0] == "ERROR") {
                    // if an ERROR is returned, say as much
                    // return error status?
                    $w->ctx("showreport", "error");
                } elseif ($tbl[0][0] == "SUCCESS") {
                    // if a SUCCESSFUL insert/update/delete is returned, say as much
                    // return error status?
                    $w->ctx("showreport", "success");
                } else {
                    // if we have records, present them in the requested format
                    // as a cvs file for download
                    if ($_REQUEST['format'] == "csv") {
                        ReportService::getInstance($w)->exportcsv($tbl, $rep->title);
                    } elseif ($_REQUEST['format'] == "pdf") {
                        // as a PDF file for download
                        ReportService::getInstance($w)->exportpdf($tbl, $rep->title);
                    } elseif ($_REQUEST['format'] == "xml") {
                        // as XML document for download
                        ReportService::getInstance($w)->exportxml($tbl, $rep->title);
                    } else {
                        // if confused, display a web page in the usual manner
                        $results = "";
                        foreach ($tbl as $t) {
                            $crumbs = array_shift($t);
                            $title = array_shift($t);
                            $results .= "<b>" . $title . "</b><p>" . HtmlBootstrap5::table($t, null, "tablesorter", true);
                        }
                        $w->ctx("showreport", $results);
                    }
                }
            }
        }
    }
}
