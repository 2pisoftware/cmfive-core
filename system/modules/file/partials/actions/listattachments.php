<?php namespace System\Modules\File;

function listattachments(\Web $w, $params) {
	$object = $params['object'];
	$redirect = $params['redirect'];
	
	$w->ctx("attachments", $w->File->getAttachments($object, !empty($object->id) ? $object->id : null));
	$w->ctx("redirect",$redirect);
	$w->ctx("object",$object);
}