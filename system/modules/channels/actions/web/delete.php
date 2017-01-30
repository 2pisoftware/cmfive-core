<?php

function delete_GET(Web $w) {
	$p = $w->pathMatch("id");
	$id = $p["id"];

	if ($id) {
		$channel = $w->Channel->getWebChannel($id);
		$channel->delete();

		$w->msg(__("Channel deleted"), "/channels/listchannels");
	} else {
		$w->error(__("Could not find channel"));
	}
}
