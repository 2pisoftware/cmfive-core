<?php namespace System\Modules\Main;

function removeUser_ALL(\Web $w, $params = []) {
	$deleting_user = $params['user'];
	$redirect = $params['redirect'];

	$owner_links = $w->Main->getObjects("RestrictedObjectUserLink", ["user_id" => $deleting_user->id, "type" => "owner"]);
	$owner_links_array = [];

	foreach (empty($owner_links) ? [] : $owner_links as $owner_link) {
		$owner_links_array[] = $owner_link->toArray();
	}

	$users = $w->Auth->getUsers();
	$users_array = [];

	foreach ($users as $user) {
		if ($user->id === $deleting_user->id) {
			continue;
		}

		$users_array[] = [
			"id" => $user->id,
			"name" => $user->getFullName()
		];
	}

	$w->ctx("deleting_user_id", $deleting_user->id);
	$w->ctx("users", json_encode($users_array));
	$w->ctx("redirect", $redirect);
	$w->ctx("owner_links", json_encode($owner_links_array));
}