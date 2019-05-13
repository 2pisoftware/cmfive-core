<?php

function moveToAdapter_GET(Web $w) {

	$from_adapter = $w->request('from_adapter');
	$to_adapter = $w->request('to_adapter');

	if (!empty(Config::get('file.adapters.' . $to_adapter)) && Config::get('file.adapters.' . $to_adapter . '.active') === true) {

		if (empty(Config::get('file.adapters.' . $from_adapter))) {
			$w->error('Origin adapter "' . $from_adapter . '" is not found', '/file-admin');
		}

		// From index
		$count = 0;
		$attachments = $w->File->getAttachmentsForAdapter($from_adapter);
		if (!empty($attachments)) {
			foreach($attachments as $attachment) {
				$attachment->moveToAdapter($to_adapter);
				$count++;
			}
		}
		$w->msg($count . ' attachment' . ($count == 1 ? '' : 's') . ' moved from "' . $from_adapter . '" to "' . $to_adapter . '"', '/file-admin');

	} else {
		$w->error('Target adapter "' . $to_adapter . '" is either not found or not active', '/file-admin');
	}

}