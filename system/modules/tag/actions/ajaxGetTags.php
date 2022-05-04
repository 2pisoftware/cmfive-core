<?php

function ajaxGetTags_GET(Web $w) {
	
	$w->setLayout(null);
	list($class, $id) = $w->pathMatch();
	
	if (empty($class) || empty($id) || !class_exists($class)) {
		return;
	}
	
	$object = TagService::getInstance($w)->getObject($class, $id);
	if (empty($object->id)) {
		return;
	}
	
	$tags = TagService::getInstance($w)->getTagsByObject($object);
	$filtered_tags = ['display' => [], 'hover' => []];
	
	$tag_str_len = 0;
	if (!empty($tags)) {
		foreach($tags as $tag) {
			if ($tag_str_len < 10) {
				$filtered_tags['display'][] = $tag->toArray();
			} else {
				$filtered_tags['hover'][] = $tag->toArray();
			}
			
			$tag_str_len += strlen($tag->tag);
		}
	}

	$w->out(json_encode($filtered_tags));
	
}