<?php

function main_core_dbobject_after_update(Web $w, $object) {
	if ((property_exists($object, "is_deleted") && $object->is_deleted == 1)) {
		// $w->Log->debug("Removing history object: " . get_class($object) . " ID: " . $object->id);
		History::remove($object);
	}
}

function main_core_dbobject_after_delete(Web $w, $object) {
	// $w->Log->debug("Removing object: " . get_class($object) . " ID: " . $object->id);
	History::remove($object);
}
