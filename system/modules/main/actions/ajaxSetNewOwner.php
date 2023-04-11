<?php

function ajaxSetNewOwner_POST(Web $w) {
	$w->setLayout(null);

	$request_data = json_decode(file_get_contents("php://input"));
	if (empty($request_data)) {
		$w->out((new AxiosResponse())->setErrorResponse(null, ["error_message" => "Missing request data"]));
		return;
	}

	$viewer_links = MainService::getInstance($w)->getObjects("RestrictedObjectUserLink", ["user_id" => $request_data->deleting_user_id, "type" => "viewer"]);
	foreach (empty($viewer_links) ? [] : $viewer_links as $viewer_link) {
		$viewer_link->delete();
	}

	$owned_object = [];
	foreach ($request_data->owner_links as $owner_links) {
		$owned_object[] = MainService::getInstance($w)->getObject($owner_links->object_class, $owner_links->object_id);
	}

	foreach ($owned_object as $owned_object) {
		RestrictableService::getInstance($w)->setOwner($owned_object, $request_data->new_owner_id);
	}

	$w->out((new AxiosResponse())->setSuccessfulResponse("OK", []));
}