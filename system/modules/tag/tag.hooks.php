<?php

function tag_core_dbobject_add_to_index($w, $obj) {
	$tags = $w->Tag->getTagsByObject($obj->id, get_class($obj));
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
