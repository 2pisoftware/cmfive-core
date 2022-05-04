<?php
function view_GET(Web $w) {
	InboxService::getInstance($w)->navigation($w,"Message View");
	$p = $w->pathMatch("type","id");
	$msg = InboxService::getInstance($w)->getMessage($p['id']);
	if (!$msg) {
		$w->error("No such message.");
	}
	if ($msg->user_id != AuthService::getInstance($w)->user()->id) {
		$w->error("No access.");
	}
	$msg->is_new = 0;
	$msg->dt_read = time();
	$msg->update();
	$w->ctx("message",$msg);
	$w->ctx("type",$p['type']);
}