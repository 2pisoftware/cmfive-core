<?php

function index_GET(Web $w) {
	
	list($id) = $w->pathMatch("id");
	$adapter = $w->request("adapter");
	
	// If ID and adapter are set then transfer file
	if (!empty($id) && !empty($adapter)) {
		$attachment = $w->File->getAttachment($id);
		if (!empty($attachment) && $attachment->exists()) {
			$attachment->moveToAdapter($adapter);
			$w->msg("File moved to " . $adapter, "/admin-file");
		}
	} else {
		// get attachments
		$attachments = $w->File->getObjects("Attachment", ["is_deleted" => 0]);

		$adapters = array_keys(Config::get('file.adapters'));

		$sorted_attachments = [];
		if (!empty($attachments)) {
			foreach($attachments as $attachment) {
				$sorted_attachments[$attachment->adapter][] = $attachment;
			}
		}

		$w->ctx("adapters", $adapters);
		$w->ctx("attachments", $sorted_attachments);
	}
	
}
