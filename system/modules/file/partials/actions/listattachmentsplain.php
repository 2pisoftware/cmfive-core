<?php

function listattachmentsplain_ALL($w, $params) {
	$object = $params['object'];
	$redirect = $params['redirect'];
	
	$w->ctx("attachments", FileService::getInstance($w)->getAttachments($object, !empty($object->id) ? $object->id : null));
	$w->ctx("redirect",$redirect);
	$w->ctx("object",$object);
}
