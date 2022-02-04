<?php

function main_core_dbobject_after_update(Web $w, $object) {
	if ((property_exists($object, "is_deleted") && $object->is_deleted == 1)) {
		History::remove($object);
	}
}

function main_core_dbobject_after_delete(Web $w, DbObject $object) {
	History::remove($object);

	if (property_exists($object, "_restrictedable") && !$object->_restrictable) {
		return;
	}

	$links = MainService::getInstance($w)->getObjects("RestrictedObjectUserLink", ["object_id" => $object->id, "object_class" => get_class($object)]);

	foreach (empty($links) ? [] : $links as $link) {
		$link->delete();
	}
}

function main_admin_remove_user(Web $w, User $user) {
	$owner_links = MainService::getInstance($w)->getObjects("RestrictedObjectUserLink", ["user_id" => $user->id, "type" => "owner"]);
	if (empty($owner_links)) {
		return;
	}

	return $w->partial("removeUser", ["user" => $user, "redirect" => "/admin-user/remove/" . $user->id, "owner_links" => $owner_links], "main");
}