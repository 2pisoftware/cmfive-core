<?php

function ajaxEditAttachment_POST(Web $w) {
	$w->setLayout(null);

	$user = $w->Auth->user();
	if (!$user->hasRole("restrict")) {
		$w->out((new AxiosResponse())->setErrorResponse(null, ["error_message" => "User not authorised to restrict objects"]));
		return;
	}

	$request_data = json_decode($_POST["file_data"]);
	if (empty($request_data)) {
		$w->out((new AxiosResponse())->setErrorResponse(null, ["error_message" => "Missing attachment data"]));
		return;
	}

	$attachment = $w->File->getAttachment($request_data->id);
	if (empty($attachment)) {
		$w->out((new AxiosResponse())->setErrorResponse(null, ["error_message" => "Missing attachment"]));
		return;
	}

	$owner = $attachment->getOwner();
	if (empty($owner) || $owner->id !== $user->id) {
		$w->out((new AxiosResponse())->setErrorResponse(null, ["error_message" => "User not authorised to restrict objects"]));
		return;
	}

	$attachment->updateAttachment("file");
	$attachment->title = $request_data->title;
	$attachment->description = $request_data->description;
	$attachment->creator_id = $request_data->new_owner->id;
	$attachment->update();

	if ($request_data->is_restricted) {
		$attachment->setOwner($request_data->new_owner->id);

		$current_viewers = $attachment->getViewerLinks();
		foreach (empty($current_viewers) ? [] : $current_viewers as $current_viewer) {
			$current_viewer->delete();
		}

		foreach (!empty($request_data->viewers) ? $request_data->viewers : [] as $viewer) {
			$attachment->addViewer($viewer->id);
		}
	} else {
		$owner = $attachment->getOwnerLink();
		if (!empty($owner)) {
			$owner->delete();
		}

		$viewers = $attachment->getViewerLinks();
		foreach (empty($viewers) ? [] : $viewers as $viewer) {
			$viewer->delete();
		}
	}

	$w->out((new AxiosResponse())->setSuccessfulResponse("OK", []));
}