<?php

function changeTags_GET(Web $w) {
	
	list($object_class, $id) = $w->pathMatch();
	
	if (empty($object_class) || empty($id)) {
		$w->ctx('error', 'Could not load taggable object');
		return;		
	}
	
	$object = $w->Tag->getObject($object_class, $id);
	if (empty($object->id)) {
		$w->ctx('error', 'Could not find object to tag');
		return;
	}
	
	$w->ctx('object', $object);
	$w->ctx('object_class', $object_class);
	$w->ctx('id', $id);
	
	// Get current tags
	$w->ctx('tags', $w->Tag->getTagsByObject($object));
	$w->ctx('object_tags', $w->Tag->getTagsByObjectClass($object_class));
	$w->ctx('all_tags', $w->Tag->getTags());
	
}

function changeTags_POST(Web $w) {
	var_dump($_POST); die();
}