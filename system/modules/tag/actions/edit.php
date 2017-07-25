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
	
	$tag = $w->Tag->getTag($p['id']);
	$tag->tag = $w->request('tag');
	$tag->update();
	
	$w->msg("Tag saved", "/tag/edit/".$t->id);
	
}