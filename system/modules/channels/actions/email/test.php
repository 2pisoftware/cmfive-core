<?php
function test_GET(Web $w ) {
	$p = $w->pathMatch("id");
	$id = $p["id"];

	if ($id) {
		$channel = $w->Channel->getEmailChannel($id);
		echo $channel->connectToMail(true, true);
	} else {
		$w->error("Could not find channel");
	}
}?>
