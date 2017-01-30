<?php
function deleteforever_ALL(Web &$w) {
	$p = $w->pathMatch("arr");
	$check = explode(",", $p['arr']);
	if ($check[0] == "on") {
		unset($check[0]);
	}
	foreach ($check as $message) {
		$mess_obj = $w->Inbox->getMessage($message);
		$mess_obj->del_forever = 1;
		//		$mess_obj->dt_archived = time();
		$mess_obj->update();
	}
	$w->msg(__("Message(s) Deleted"), "/inbox/trash");
}
