<?php

function index_GET(Web $w) {
	
	$w->ctx('title', 'File adapter management');

	// Get attachments
	$attachments = $w->File->getObjects("Attachment", ["is_deleted" => 0]);

	$adapters = array_keys(Config::get('file.adapters'));

	$sorted_attachments = [];
	foreach($adapters as $adapter) {
		$sorted_attachments[$adapter] = [];
	}

	if (!empty($attachments)) {
		foreach($attachments as $attachment) {
			$sorted_attachments[$attachment->adapter][] = $attachment;
		}
	}

	$w->ctx("adapters", $adapters);
	$w->ctx("attachments", $sorted_attachments);
	
}
