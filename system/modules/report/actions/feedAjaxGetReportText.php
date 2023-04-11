<?php
// when creating a feed, display the details of a report when it is selected as the feed input
function feedAjaxGetReportText_ALL(Web $w) {
	// get the relevant report
	$rep = ReportService::getInstance($w)->getReportInfo($_REQUEST["id"]);

	if ($rep) {
		$feedtext = "<table border=0 class=form>" .
		           "<tr><td class=section colspan=2>Report</td></tr>" . 
				   "<tr><td><b>Title</td><td>" . $rep->title . "</td></tr>" . 
				   "<tr><td><b>Description</b></td><td>" . $rep->description . "</td></tr>" . 
				   "</table><p>";
	}
	else {
		$feedtext = "";
	}

	$w->setLayout(null);
	$w->out(json_encode($feedtext));
}

