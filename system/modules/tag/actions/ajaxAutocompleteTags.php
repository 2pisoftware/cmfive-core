<?php

function ajaxAutocompleteTags_GET(Web $w) {
	$w->setLayout(null);
	$term = $w->request("term");
        $tags = [];
        
        if ($term == ' ') {
            $tags = $w->Tag->getObjects("Tag", null, false, true, "tag");
        }
        else {
            $tags = $w->Tag->getObjects("Tag", ["tag LIKE ?" => "%{$term}%"], false, true, "tag");
        }
	
	$return_data = [];
	if (!empty($tags)) {
            foreach($tags as $tag) {
                    $return_data[] = ["label" => $tag->tag, "value" => $tag->id];
            }
	}
	
	echo json_encode($return_data);
}