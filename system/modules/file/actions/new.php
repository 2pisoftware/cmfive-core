<?php

function new_GET(Web $w) {
	$redirect_url = $w->request("redirect_url");
	$redirect_url = defaultVal($redirect_url, defaultVal($_SERVER["REQUEST_URI"], "/"));

	$p = $w->pathMatch("class", "class_id");
	if (empty($p['class']) || empty($p['class_id'])) {
		$w->error("Missing class parameters", $redirect_url);
	}

	$viewers = $w->db->get("user")
		->select()
		->select("user.id, contact.firstname, contact.lastname")
		->leftJoin("contact ON contact.id = user.contact_id")
		->where("user.is_deleted", 0)
		->where("user.id != ?", $w->Auth->user()->id)
		->fetchAll();

	foreach ($viewers as &$viewer) {
		$viewer["can_view"] = false;
	}

	$w->ctx("redirect_url", WEBROOT . "/" . $redirect_url);
	$w->ctx("class", $p["class"]);
	$w->ctx("class_id", $p["class_id"]);
	$w->ctx("viewers", json_encode($viewers));
	$w->ctx("can_restrict", $w->Auth->user()->hasRole("restrict") ? "true" : "false");
}
