<?php

function ajaxEditAttachment_POST(Web $w) {
	$w->setLayout(null);

	$request_data = json_decode($_POST["file_data"]);
	if (empty($request_data)) {
		$w->out((new AxiosResponse())->setErrorResponse(null, ["error_message" => "Missing attachment data"]));
		return;
	}

	$user = AuthService::getInstance($w)->user();
	if (isset($request_data->is_restricted) && $request_data->is_restricted && !$user->hasRole("restrict")) {
		$w->out((new AxiosResponse())->setErrorResponse(null, ["error_message" => "User not authorised to restrict objects"]));
		return;
	}

	$attachment = FileService::getInstance($w)->getAttachment($request_data->id);
	if (empty($attachment)) {
		$w->out((new AxiosResponse())->setErrorResponse(null, ["error_message" => "Missing attachment"]));
		return;
	}

	$owner = RestrictableService::getInstance($w)->getOwner($attachment);
	if (!empty($owner) && $owner->id !== $user->id) {
		$w->out((new AxiosResponse())->setErrorResponse(null, ["error_message" => "User not authorised to restrict objects"]));
		return;
	}

	$attachment->updateAttachment("file");
	$attachment->title = property_exists($request_data, "title") ? $request_data->title : null;
	$attachment->description = property_exists($request_data, "description") ? $request_data->description : null;
	$attachment->creator_id = property_exists($request_data, "new_owner") ? $request_data->new_owner->id : null;
	$attachment->update();

	if (property_exists($request_data, "is_restricted") && $request_data->is_restricted) {
		RestrictableService::getInstance($w)->setOwner($attachment, $request_data->new_owner->id);

		$current_viewers = RestrictableService::getInstance($w)->getViewerLinks($attachment);
		foreach (empty($current_viewers) ? [] : $current_viewers as $current_viewer) {
			$current_viewer->delete();
		}

		foreach (!empty($request_data->viewers) ? $request_data->viewers : [] as $viewer) {
			RestrictableService::getInstance($w)->addViewer($attachment, $viewer->id);
		}
	} else {
		$owner = RestrictableService::getInstance($w)->getOwnerLink($attachment);
		if (!empty($owner)) {
			$owner->delete();
		}

		$viewers = RestrictableService::getInstance($w)->getViewerLinks($attachment);
		foreach (empty($viewers) ? [] : $viewers as $viewer) {
			$viewer->delete();
		}
	}

	$w->out((new AxiosResponse())->setSuccessfulResponse("OK", []));
}