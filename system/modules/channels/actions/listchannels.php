<?php

function listchannels_GET(Web $w) {
	$w->Channels->navigation($w, "Channels List");
	
        // Get known channel types: email and web
	$email_channels = $w->Channel->getEmailChannels();
	$web_channels = $w->Channel->getWebChannels();

	$w->ctx("channels", array_merge((isset($email_channels)) ? $email_channels : array(),
                                        (isset($web_channels)) ? $web_channels : array()));
}