<?php

function edit_GET(Web $w) {
	$redirect_url = $w->request("redirect_url");
	$redirect_url = defaultVal($redirect_url, defaultVal($_SERVER["REQUEST_URI"], "/"));

	list($attachment_id) = $w->pathMatch("id");
	if (empty($attachment_id)) {
		$w->error("Missing attachment ID", $redirect_url);
	}

	$attachment = $w->File->getAttachment($attachment_id);
	if (empty($attachment)) {
		$w->error("Attachment not found", $redirect_url);
	}

	$owner = $attachment->getOwner();
	$object = $attachment->getParent();
	$users = $w->Auth->getUsers();
	$viewers = [];

	if (!empty($object)) {
		foreach (empty($users) ? [] : $users as $user) {
			if ($user->id === $w->Auth->user()->id) {
				continue;
			}

			if ($object->canView($user)) {
				$link = $w->Main->getObject("RestrictedObjectUserLink", ["object_id" => $attachment->id, "user_id" => $user->id, "type" => "viewer"]);

				$viewers[] = [
					"id" => $user->id,
					"name" => $user->getFullName(),
					"can_view" => empty($link) ? false : true
				];
			}
		}
	}

	$w->ctx("id", $attachment->id);
	$w->ctx("title", $attachment->title);
	$w->ctx("description", $attachment->description);
	$w->ctx("file_name", $attachment->filename);
	$w->ctx("file_directory", WEBROOT . "/file/atfile/" . $attachment->id . "/" . $attachment->filename);
	$w->ctx("redirect_url", WEBROOT . "/" . $redirect_url);
	$w->ctx("is_restricted", json_encode(empty($owner) ? false : true));
	$w->ctx("viewers", json_encode($viewers));
	$w->ctx("owner", json_encode(["id" => $owner->id, "name" => $owner->getFullName()]));
	$w->ctx("can_restrict", $w->Auth->user()->hasRole("restrict") ? "true" : "false");
}