<?php

function new_GET(Web $w) {
	//VueComponentRegister::registerComponent("RestrictForm", new \VueComponent("RestrictForm", "/system/templates/vue-components/restrict-form.js"));

	$redirect_url = $w->request("redirect_url");
	$redirect_url = defaultVal($redirect_url, defaultVal($_SERVER["REQUEST_URI"], "/"));

	$p = $w->pathMatch("class", "class_id");
	if (empty($p['class']) || empty($p['class_id'])) {
		$w->error("Missing class parameters", $redirect_url);
	}

	$object = $w->File->getObject($p["class"], $p["class_id"]);
	$users = $w->Auth->getUsers();
	$viewers = [];

	if (!empty($object)) {
		foreach ($users as $user) {
			if ($user->id === $w->Auth->user()->id) {
				continue;
			}

			if ($object->canView($user)) {
				$contact = $user->getContact();

				$viewers[] = [
					"id" => $user->id,
					"firstname" => empty($contact) ? null : $contact->firstname,
					"lastname" => empty($contact) ? null : $contact->lastname,
					"can_view" => false
				];
			}
		}
	}

	$w->ctx("redirect_url", WEBROOT . "/" . $redirect_url);
	$w->ctx("class", $p["class"]);
	$w->ctx("class_id", $p["class_id"]);
	$w->ctx("viewers", json_encode($viewers));
	$w->ctx("can_restrict", Attachment::$_restrictable && $w->Auth->user()->hasRole("restrict") ? "true" : "false");
}
