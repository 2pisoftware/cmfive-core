<?php

function edit_GET(Web $w) {
	$w->Tag->navigation($w,__("Edit Tag"));
	$p = $w->pathMatch("id");
	
	$t = $w->Tag->getTag($p['id']);
	$newForm = array();
	$newForm[__("Tag")] = array(
		array(
			array(__("Tag"), "text", "tag",$t->tag),
		)
	);

	$w->ctx("edittagform", Html::multiColForm($newForm, $w->localUrl('/tag/edit/'.$t->id)));
}

function edit_POST(Web $w) {
	$p = $w->pathMatch("id");
	$t = $w->Tag->getTag($p['id']);
	$r = $w->Tag->renameTag($t->tag, $_POST['tag']);
	if(-1 === $r) {
		$w->msg(__("Couldn't save tag").", \"".$_POST['tag']."\" ".__("already exists!"), "/tag/edit/".$t->id);
	} else {
		$w->msg(__("Tag saved"), "/tag/edit/".$t->id);
	}
}
