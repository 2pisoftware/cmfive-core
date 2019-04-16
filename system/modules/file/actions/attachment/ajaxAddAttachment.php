<?php

function ajaxAddAttachment_POST(Web $w) {
	$w->setLayout(null);

	$request_data = json_decode($_POST["file_data"]);
	if (empty($request_data)) {
		$w->out((new AxiosResponse())->setErrorResponse(null, ["error_message" => "Missing attachment data"]));
		return;
	}

	$user = $w->Auth->user();
	if (isset($request_data->is_restricted) && $request_data->is_restricted && !$user->hasRole("restrict")) {
		$w->out((new AxiosResponse())->setErrorResponse(null, ["error_message" => "User not authorised to restrict objects"]));
		return;
	}

	$object = $w->File->getObject($request_data->class, $request_data->class_id);
	if (empty($object)) {
		$w->out((new AxiosResponse())->setErrorResponse(null, ["error_message" => "Missing attachment class"]));
		return;
	}

	$attachment_id = $w->File->uploadAttachment("file", $object, isset($request_data->title) ? $request_data->title : null, isset($request_data->description) ? $request_data->description : null);
	if (empty($attachment_id)) {
		$w->out((new AxiosResponse())->setErrorResponse(null, ["error_message" => "Failed to add attachment"]));
		return;
	}

	if (isset($request_data->is_restricted) && $request_data->is_restricted) {
		$attachment = $w->File->getAttachment($attachment_id);
		$attachment->setOwner($user->id);

		foreach (!empty($request_data->viewers) ? $request_data->viewers : [] as $viewer) {
			$attachment->addViewer($viewer->id);
		}
	}

	$w->out((new AxiosResponse())->setSuccessfulResponse("OK", []));
}