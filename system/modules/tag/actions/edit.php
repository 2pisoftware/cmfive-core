<?php

function edit_GET(Web $w) {
	TagService::getInstance($w)->navigation($w,"Edit Tag");
	$p = $w->pathMatch("id");
	
	$t = TagService::getInstance($w)->getTag($p['id']);
	$newForm = array();
	$newForm["Tag"] = array(
		array(
			array("Tag", "text", "tag",$t->tag),
		)
	);

	$w->ctx("edittagform", Html::multiColForm($newForm, $w->localUrl('/tag/edit/'.$t->id)));
}

function edit_POST(Web $w) {
	$p = $w->pathMatch("id");
	
	$existing_tag = TagService::getInstance($w)->getObject("Tag", ['tag' => trim(strip_tags(Request::string('tag'))), 'is_deleted' => 0]);
	if (!empty($existing_tag)) {
		$w->error("Tag named '" . Request::string('tag') . "' already exists.", '/tag/edit/' . $p['id']);
	}

	$tag = TagService::getInstance($w)->getTag($p['id']);
	$tag->tag = trim(strip_tags(Request::string('tag')));
	$tag->update();
	
	$w->msg("Tag saved", "/tag/edit/" . $tag->id);
	
}