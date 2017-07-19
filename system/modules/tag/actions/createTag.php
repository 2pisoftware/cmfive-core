<?php

function createTag_GET(Web $w) {
	$w->setLayout(null);
	
	list($class, $id) = $w->pathMatch();
	$new_tag = $w->request('tag');
	
	if (empty($class) || empty($id) || empty($new_tag)) {
		return;
	}
	
	if (!class_exists($class)) {
		return;
	}
	
	$tag = new Tag($w);
	$tag->obj_class = $class;
	$tag->obj_id = $id;
	$tag->tag = $new_tag;
	$tag->insert();
	
	$w->out(json_encode(['id' => $tag->id, 'tag' => $tag->tag], JSON_FORCE_OBJECT));
	
}