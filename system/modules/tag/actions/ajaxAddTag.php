<?php

function ajaxAddTag_POST(Web $w) {
    return ajaxAddTag_GET($w);
}

function ajaxAddTag_GET(Web $w) {
    $w->setLayout(null);
    
    list($class, $id) = $w->pathMatch();
    $tag_id = Request::int("value") ?? Request::int('_tag_id');
    
    if (empty($class) || empty($id) || empty($tag_id)) {
        return;
    }
    
    if (!class_exists($class)) {
        return;
    }
    
    // Check that tag and object target exist
    $object_target = TagService::getInstance($w)->getObject($class, $id);
    if (empty($object_target->id)) {
        return;
    }
    
    $tag = TagService::getInstance($w)->getTag($tag_id);
    if (empty($tag->id)) {
        return;
    }
    
    // Check that tag isn't already assigned
    $existing_tag_assign = TagService::getInstance($w)->getObject('TagAssign', ['object_class' => $class, 'object_id' => $id, 'tag_id' => $tag->id, 'is_deleted' => 0]);
    if (empty($existing_tag_assign)) {
        // Create if it doesnt exist
        $tag_assign = new TagAssign($w);
        $tag_assign->object_class = get_class($object_target);
        $tag_assign->object_id = $object_target->id;
        $tag_assign->tag_id = $tag->id;
        $tag_assign->insert();
    }
    
    $w->out(json_encode(['id' => $tag->id, 'tag' => $tag->tag], JSON_FORCE_OBJECT));
    
}