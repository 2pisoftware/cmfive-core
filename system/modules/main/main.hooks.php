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
