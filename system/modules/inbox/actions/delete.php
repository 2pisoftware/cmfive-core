<?php
function delete_ALL(Web &$w) {
	$p = $w->pathMatch("type", "arr");
	$check = explode(",", $p['arr']);
	if ($check[0] == "on") {
		unset($check[0]);
	}
	foreach ($check as $message) {
		$mess_obj = InboxService::getInstance($w)->getMessage($message);
		$mess_obj->is_deleted = 1;
		$mess_obj->is_new=0;
		//		$mess_obj->dt_archived = time();
		$mess_obj->update();
	}
	$w->msg("Message(s) Deleted", "/inbox/" . ($p['type'] == "new" ? "" : $p['type']));
}
