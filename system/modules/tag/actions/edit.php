<?php

function edit_GET(Web $w) {
	$w->Tag->navigation($w,"Edit Tag");
	$p = $w->pathMatch("id");
	
	$t = $w->Tag->getTag($p['id']);
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
	
	$existing_tag = $w->Tag->getObject("Tag", ['tag' => trim(strip_tags($w->request('tag'))), 'is_deleted' => 0]);
	if (!empty($existing_tag)) {
		$w->error("Tag named '" . $w->request('tag') . "' already exists.", '/tag/edit/' . $p['id']);
	}

	$tag = $w->Tag->getTag($p['id']);
	$tag->tag = trim(strip_tags($w->request('tag')));
	$tag->update();
	
	$w->msg("Tag saved", "/tag/edit/" . $tag->id);
	
}