<?php

function callchannel_ALL(Web $w) {
	$w->setLayout(null);
	$p = $w->pathMatch("id");
	$id = $p["id"];

	$channel = $w->Channel->getEmailChannel($id);
	$channel->doJob();

}