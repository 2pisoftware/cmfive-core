<?php
function editfeed_GET(Web &$w) {
	$w->Report->navigation($w, "Edit Feed");

	$p = $w->pathMatch("id");

	$feed = $w->Report->getFeedInfobyId($p["id"]);

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

	$note = "Available Formats: html, csv, pdf, xml<br>";
	$note .= "Date Formats must be <b>d/m/Y</b> to mimic date picker";

	$f = Html::form(array(
	array("Create a Feed from a Report","section"),
	array("Select Report","select","rid",$feed->report_id,$myrep),
	array("Feed Title","text","title",$feed->title),
	array("Description","textarea","description",$feed->description,"40","6"),
	array("Feed URL","static","url", $feed->url),
	array("Note","static","url", $note),
	),$w->localUrl("/report/editfeed/".$feed->id),"POST"," Update ");

	$w->ctx("editfeed",$f);
}

function editfeed_POST(Web &$w) {
	$w->Report->navigation($w, "Create a Feed");

	$p = $w->pathMatch("id");

	$feed = $w->Report->getFeedInfobyId($p["id"]);

	$arr["report_id"] = $_REQUEST["rid"];
	$arr["title"] = $_REQUEST["title"];
	$arr["description"] = $_REQUEST["description"];

	$feed->fill($arr);
	$feed->update();

	$w->msg("Feed " . $feed->title . " has been updated","/report/listfeed/");
}
