<?php

function ajaxDeleteAttachment_POST(Web $w) {
	$w->setLayout(null);

	$request_data = json_decode(file_get_contents("php://input"));
	if (empty($request_data)) {
		$w->out((new AxiosResponse())->setErrorResponse(null, ["error_message" => "Missing data"]));
		return;
	}

	$attachment = FileService::getInstance($w)->getAttachment($request_data->attachment_id);
	if (empty($attachment)) {
		$w->out((new AxiosResponse())->setErrorResponse(null, ["error_message" => "Attachment not found"]));
		return;
	}

	$file = $attachment->getFile();
	if (empty($file)) {
		$w->out((new AxiosResponse())->setErrorResponse(null, ["error_message" => "Attachment file not found"]));
		return;
	}

	try {
		$file->delete();
	} catch(\Gaufrette\Exception\FileNotFound $fnf) {
		LogService::getInstance($w)->setLogger("FILE")->warning("Trying to permanently elete file but it doesn't exist in the " . $attachment->adapter . " adapter");
	}

	$attachment->delete(true);

	$w->out((new AxiosResponse())->setSuccessfulResponse("OK", []));
}