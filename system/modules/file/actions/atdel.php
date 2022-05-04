<?php
function atdel_GET(Web &$w)
{
	$p = $w->pathMatch("id", "url");
	$att = FileService::getInstance($w)->getAttachment($p['id']);
	if ($att) {
		$w->ctx('attach_id', $att->id);
		$w->ctx('attach_table', $att->parent_table);
		$w->ctx('attach_table_id', $att->parent_id);
		$w->ctx('attach_title', $att->title);
		$w->ctx('attach_description', $att->description);
		$att->delete();
		$w->msg("Attachment deleted.", "/" . str_replace(" ", "/", $p['url']));
	} else {
		$w->error("Attachment does not exist.", "/" . str_replace(" ", "/", $p['url']));
	}
}
