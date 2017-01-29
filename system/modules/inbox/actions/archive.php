<?php
function archive_ALL(Web $w) {
	$p = $w->pathMatch("type","arr");
	$type = $p['type'];
	$check = explode(",",$p['arr']);
	if ($check[0] == "on"){
		unset($check[0]);
	}
	foreach($check as $message){
		$mess_obj = $w->Inbox->getMessage($message);
		$mess_obj->is_archived = 1;
		$mess_obj->dt_archived = time();
		$mess_obj->is_new=false;
		$mess_obj->update();
	}
	$w->msg("Message(s) Archived","/inbox/".$type );
}
