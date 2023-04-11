<?php namespace System\Modules\Main;

function removeUser_ALL(\Web $w, $params = []) {
	$deleting_user = $params['user'];
	$redirect = $params['redirect'];
	$owner_links = $params["owner_links"];


	$owner_links_array = [];
	foreach (empty($owner_links) ? [] : $owner_links as $owner_link) {
		$owner_links_array[] = $owner_link->toArray();
	}

	$restricted_object_classes = [];
	foreach ($owner_links_array as $owner_link_array) {
		if (!array_key_exists($owner_link_array["object_class"], $restricted_object_classes)) {
			$restricted_object_classes[$owner_link_array["object_class"]] = ["name" => $owner_link_array["object_class"], "count" => 1];
			continue;
		}

		foreach ($restricted_object_classes as &$restricted_object_class) {
			if ($owner_link_array["object_class"] == $restricted_object_class["name"]) {
				++$restricted_object_class;
			}
		}
	}

	$users = AuthService::getInstance($w)->getUsers();
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
	$w->ctx("restricted_object_classes", json_encode($restricted_object_classes));
}