<?php
function archive_ALL(Web $w) {
	$p = $w->pathMatch("type","arr");
	$type = $p['type'];
	$check = explode(",",$p['arr']);
	if ($check[0] == "on"){
		unset($check[0]);
	}
	foreach($check as $message){
		$mess_obj = InboxService::getInstance($w)->getMessage($message);
		$mess_obj->is_archived = 1;
		$mess_obj->dt_archived = time();
		$mess_obj->is_new= 0;
		$mess_obj->update();
	}
	$w->msg("Message(s) Archived","/inbox/". ($type == "new" ? "" : $type));
}
