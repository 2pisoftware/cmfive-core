<?php
function deletefeed_ALL(Web &$w) {
	$p = $w->pathMatch("id");

	$feed = ReportService::getInstance($w)->getFeedInfobyId($p["id"]);

	$arr["is_deleted"] = 1;

	$feed->fill($arr);
	$feed->update();

	$w->msg("Feed " . $feed->title . " has been deleted","/report/listfeed/");
}
