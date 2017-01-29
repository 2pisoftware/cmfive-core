<?php
// receive, process and deliver feeds
function feed_ALL(Web &$w) {
	// check for feed key in request
	if (array_key_exists("key",$_REQUEST)) {
		// get feed
		$feed = $w->Report->getFeedInfobyKey($_REQUEST["key"]);

		// if feed, then get respective report details
		if ($feed) {
			$rep = $w->Report->getReportInfo($feed->report_id);
				
			// if report exists, execute it
			if ($rep) {
				$w->Report->navigation($w, $rep->title);

				// prepare and execute the report
				$tbl = $rep->getReportData();

				// if we have an empty return, say as much
				if (!$tbl) {
					// return error status?
				}
				// if an ERROR is returned, say as much
				elseif ($tbl[0][0] == "ERROR") {
					// return error status?
					$w->ctx("showreport","error");
				}
				// if a SUCCESSFUL insert/update/delete is returned, say as much
				elseif ($tbl[0][0] == "SUCCESS") {
					// return error status?
					$w->ctx("showreport","success");
				}
				// if we have records, present them in the requested format
				else {
					// as a cvs file for download
					if ($_REQUEST['format'] == "csv") {
						$w->Report->exportcsv($tbl, $rep->title);
					}
					// as a PDF file for download
					elseif ($_REQUEST['format'] == "pdf") {
						$w->Report->exportpdf($tbl, $rep->title);
					}
					// as XML document for download
					elseif ($_REQUEST['format'] == "xml") {
						$w->Report->exportxml($tbl, $rep->title);
					}
					// if confused, display a web page in the usual manner
					else {
						foreach ($tbl as $t) {
							$crumbs = array_shift($t);
							$title = array_shift($t);
							$results .= "<b>" . $title . "</b><p>" . Html::table($t,null,"tablesorter",true);
						}
						$w->ctx("showreport",$results);
					}
				}
			}
		}
	}
}
