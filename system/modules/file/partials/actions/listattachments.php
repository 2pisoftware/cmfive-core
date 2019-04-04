<?php namespace System\Modules\File;

function listattachments(\Web $w, $params) {
	$object = $params['object'];
	$redirect = $params['redirect'];

	$attachments = $w->File->getAttachments($object, !empty($object->id) ? $object->id : null);
	if (!empty($attachments)) {
		foreach ($attachments as $attachment) {
			if (!$attachment->canView($w->Auth->user())) {
				unset($attachment);
			}
		}
	}
	
	$w->ctx("attachments", $attachments);
	$w->ctx("redirect",$redirect);
	$w->ctx("object",$object);
}