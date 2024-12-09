<?php

function ajaxCreateTag_POST(Web $w) {
    return ajaxCreateTag_GET($w);
}

function ajaxCreateTag_GET(Web $w) {
    $w->setLayout(null);
    
    list($class, $id) = $w->pathMatch();
    $new_tag = Request::string("value") ?? Request::string('tag');
    
    if (empty($class) || empty($id) || empty($new_tag)) {
        return;
    }
    
    if (!class_exists($class)) {
        return;
    }
    
    // Check if tag exists
    $tag = TagService::getInstance($w)->getObject("Tag", ['tag' => trim(strip_tags($new_tag)), 'is_deleted' => 0]);

    if (empty($tag->id)) {
        $tag = new Tag($w);
        $tag->tag = $new_tag;
        $tag->insert();
    }

    // Check that tag is not already assigned
    $tag_assign = TagService::getInstance($w)->getObject('TagAssign', ['tag_id' => $tag->id, 'object_class' => $class, 'object_id' => $id, 'is_deleted' => 0]);
    
    if (empty($tag_assign->id)) {
        // Assign object to tag
        $tag_assign = new TagAssign($w);
        $tag_assign->tag_id = $tag->id;
        $tag_assign->object_class = $class;
        $tag_assign->object_id = $id;
        $tag_assign->insert();
    }

    $w->out(json_encode(['id' => $tag->id, 'tag' => $tag->tag], JSON_FORCE_OBJECT));
    
}