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
	$t = $w->Tag->getTag($p['id']);
	$r = $w->Tag->renameTag($t->tag, $_POST['tag']);
	if(-1 === $r) {
		$w->msg("Couldn't save tag, \"".$_POST['tag']."\" already exists!", "/tag/edit/".$t->id);
	} else {
		$w->msg("Tag saved", "/tag/edit/".$t->id);
	}
}