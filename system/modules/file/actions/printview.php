<?php
function printview_GET(Web &$w) {
	$p = $w->pathMatch("table","id");
	$attachments = FileService::getInstance($w)->getAttachments($p['table'], $p['$id']);
	$w->ctx("attachments",$attachments);
	$w->setLayout(null);
}
