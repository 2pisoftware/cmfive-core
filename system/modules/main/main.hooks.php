<?php

function main_core_dbobject_after_update(Web $w, $object) {
	if ((property_exists($object, "is_deleted") && $object->is_deleted == 1)) {
		History::remove($object);
	}
}

function main_core_dbobject_after_delete(Web $w, $object) {
	History::remove($object);

	if ($object instanceof DbObject) {
		if (!$object::$_restrictable) {
			return;
		}

		$links = $w->Main->getObjects("RestrictedObjectUserLink", ["id" => $object->id]);
		if (empty($links)) {
			return;
		}

		foreach ($links as $link) {
			$link->delete();
		}
	}
}

function main_core_dbobject_after_delete_attachment(Web $w, Attachment $attachment) {
	$links = $w->File->getObjects("RestrictedObjectUserLink", ["object_id" => $attachment->id, "object_class" => "Attachment"]);
	foreach (empty($links) ? [] : $links as $link) {
		$link->delete();
	}
}

function main_core_dbobject_after_delete_comment(Web $w, Comment $comment) {
	$links = $w->File->getObjects("RestrictedObjectUserLink", ["object_id" => $comment->id, "object_class" => "Comment"]);
	foreach (empty($links) ? [] : $links as $link) {
		$link->delete();
	}
}

function main_admin_remove_user(Web $w, User $user) {
	return $w->partial("removeUser", ["user" => $user, "redirect" => "/admin-user/remove/" . $user->id], "main");
}