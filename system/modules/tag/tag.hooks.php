<?php

function tag_core_dbobject_add_to_index($w, $obj) {
	$tags = TagService::getInstance($w)->getTagsByObject($obj->id, get_class($obj));
	$words = array();
	if(!empty($tags)) {
		foreach($tags as $tag) {

			$words[] = 'unitag'.preg_replace('%[^a-z]%i', '', $tag->tag);//Add unique string so picklist can easily search tags
			$words[] = $tag->tag;//For normal user search entry
		}
		//var_dump(implode(' ', $words));
		return implode(' ', $words);
	}
}

function tag_core_dbobject_after_delete($w, $obj) {
    // delete any tags attached to deleted object
    $class_name = get_class($obj);
    $tags = TagService::getInstance($w)->getTagsByObject($obj->id, $class_name);
    if (!empty($tags)) {
        foreach ($tags as $tag) {
            $tag->delete();
        }
    }
}
