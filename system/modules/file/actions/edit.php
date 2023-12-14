<?php

function edit_GET(Web $w) {
	$redirect_url = Request::string("redirect_url");
	$redirect_url = defaultVal($redirect_url, defaultVal($_SERVER["REQUEST_URI"], "/"));

	list($attachment_id) = $w->pathMatch("id");
	if (empty($attachment_id)) {
		$w->error("Missing attachment ID", $redirect_url);
	}

	$attachment = FileService::getInstance($w)->getAttachment($attachment_id);
	if (empty($attachment)) {
		$w->error("Attachment not found", $redirect_url);
	}

	$owner = RestrictableService::getInstance($w)->getOwner($attachment);
	$object = $attachment->getParent();
	$users = AuthService::getInstance($w)->getUsers();
	$viewers = [];

	if (!empty($object)) {
		foreach (empty($users) ? [] : $users as $user) {
			if ($object->canView($user)) {
				$link = MainService::getInstance($w)->getObject("RestrictedObjectUserLink", ["object_id" => $attachment->id, "user_id" => $user->id]);

				$viewers[] = [
					"id" => $user->id,
					"name" => $user->getFullName(),
					"can_view" => empty($link) ? false : true
				];
			}
		}
	}

	usort($viewers, function($a, $b) {
		return strcmp($a["name"], $b["name"]);
	});

	$new_owner = [
		"id" => empty($owner) ? null : $owner->id,
		"name" => empty($owner) ? null : $owner->getFullName()
	];

	$w->ctx("id", $attachment->id);
	$w->ctx("title", $attachment->title);
	$w->ctx("description", $attachment->description);
	$w->ctx("file_name", $attachment->filename);
	$w->ctx("file_directory", WEBROOT . "/file/atfile/" . $attachment->id . "/" . $attachment->filename);
	$w->ctx("new_owner", json_encode($new_owner));
	$w->ctx("redirect_url", WEBROOT . "/" . $redirect_url);
	$w->ctx("is_restricted", json_encode(empty($owner) ? false : true));
	$w->ctx("viewers", json_encode($viewers));
	$w->ctx("owner", json_encode(["id" => empty($owner) ? null : $owner->id, "name" => empty($owner) ? null : $owner->getFullName()]));
	$w->ctx("show_restrict", Request::string("allowrestrictionui"));
	$w->ctx("can_restrict", AuthService::getInstance($w)->user()->hasRole("restrict") ? "true" : "false");
}