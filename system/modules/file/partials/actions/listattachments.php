<?php namespace System\Modules\File;

function listattachments(\Web $w, $params) {
	$object = $params['object'];
	$redirect = $params['redirect'];

	$attachments = $w->File->getAttachments($object, !empty($object->id) ? $object->id : null);
	if (!empty($attachments)) {
		foreach ($attachments as $key => $attachment) {
			if (!$attachment->canView($w->Auth->user())) {
				unset($attachments[$key]);
			} else {
				if (stripos($attachment->filename, '.docx') || stripos($attachment->filename, '.doc') && !$attachment->is_public) {
					$attachment->dt_viewing_window = time();
					$attachment->update();
				}
				
			}

		}
	}

	$w->ctx("attachments", $attachments);
	$w->ctx("redirect",$redirect);
	$w->ctx("object",$object);
}