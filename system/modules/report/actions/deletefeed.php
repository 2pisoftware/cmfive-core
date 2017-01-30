<?php
function deletefeed_ALL(Web &$w) {
	$p = $w->pathMatch("id");

	$feed = $w->Report->getFeedInfobyId($p["id"]);

	$arr["is_deleted"] = 1;

	$feed->fill($arr);
	$feed->update();

	$w->msg(__("Feed ") . $feed->title . __(" has been deleted"),"/report/listfeed/");
}
