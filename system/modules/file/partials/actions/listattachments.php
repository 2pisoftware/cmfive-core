<?php namespace System\Modules\File;

function listattachments(\Web $w, $params) {
	$object = $params['object'];
	$redirect = $params['redirect'];

	$attachments = $w->File->getAttachments($object, !empty($object->id) ? $object->id : null);
	$private_attachment_ids = [];
	if (!empty($attachments)) {
		foreach ($attachments as $key => $attachment) {
			if (!$attachment->canView($w->Auth->user())) {
				unset($attachments[$key]);
			} else {
				if (stripos($attachment->filename, '.docx') || stripos($attachment->filename, '.doc') && !$attachment->is_public) {
					$attachment->is_public = 1;
					$attachment->update();
					$private_attachment_ids[] = $attachment->id;
				}
				
			}

		}
	}

	$w->ctx("attachments", $attachments);
	$w->ctx("private_attachment_ids", $private_attachment_ids);
	$w->ctx("redirect",$redirect);
	$w->ctx("object",$object);
}