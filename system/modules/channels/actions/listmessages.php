<?php

function listmessages_GET(Web $w) {
	$w->Channels->navigation($w, __("Messages list"));

	$p = $w->pathMatch("id");
	$channel_id = $p["id"];

	$messages = $w->Channel->getMessages($channel_id);

	$w->ctx("messages", $messages);

	if ($channel_id) {
		$w->ctx("channel_id", $channel_id);
	}

}
