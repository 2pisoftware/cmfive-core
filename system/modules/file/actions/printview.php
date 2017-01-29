<?php
function printview_GET(Web &$w) {
	$p = $w->pathMatch("table","id");
	$attachments = $w->service("File")->getAttachments($p['table'], $p['$id']);
	$w->ctx("attachments",$attachments);
	$w->setLayout(null);
}
