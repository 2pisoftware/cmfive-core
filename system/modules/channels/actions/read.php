<?php

function read_GET(Web $w) {
	
	$w->setLayout(null);
	$p = $w->pathMatch("id");
	$id = $p["id"];

	if ($id) {
		$channel = $w->Channel->getChannel($id);
		if (!empty($channel)) {
			$channel->read();
			exit();
		}
	}

	$w->out("No channel found.");
}