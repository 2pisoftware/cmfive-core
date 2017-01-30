<?php
function view_GET(Web $w) {
	$w->Inbox->navigation($w,__("Message View"));
	$p = $w->pathMatch("type","id");
	$msg = $w->Inbox->getMessage($p['id']);
	if (!$msg) {
		$w->error(__("No such message."));
	}
	if ($msg->user_id != $w->Auth->user()->id) {
		$w->error(__("No access."));
	}
	$msg->is_new = 0;
	$msg->dt_read = time();
	$msg->update();
	$w->ctx("message",$msg);
	$w->ctx("type",$p['type']);
}
