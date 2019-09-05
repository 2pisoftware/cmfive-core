<?php

function ajaxEditAttachment_POST(Web $w) {
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

	$attachment = $w->File->getAttachment($request_data->id);
	if (empty($attachment)) {
		$w->out((new AxiosResponse())->setErrorResponse(null, ["error_message" => "Missing attachment"]));
		return;
	}

	$owner = $w->Restrictable->getOwner($attachment);
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
		$w->Restrictable->setOwner($attachment, $request_data->new_owner->id);

		$current_viewers = $w->Restrictable->getViewerLinks($attachment);
		foreach (empty($current_viewers) ? [] : $current_viewers as $current_viewer) {
			$current_viewer->delete();
		}

		foreach (!empty($request_data->viewers) ? $request_data->viewers : [] as $viewer) {
			$w->Restrictable->addViewer($attachment, $viewer->id);
		}
	} else {
		$owner = $w->Restrictable->getOwnerLink($attachment);
		if (!empty($owner)) {
			$owner->delete();
		}

		$viewers = $w->Restrictable->getViewerLinks($attachment);
		foreach (empty($viewers) ? [] : $viewers as $viewer) {
			$viewer->delete();
		}
	}

	$w->out((new AxiosResponse())->setSuccessfulResponse("OK", []));
}