<?php

function ajaxRemoveTag_POST(Web $w) {
    return ajaxRemoveTag_GET($w);
}

function ajaxRemoveTag_GET(Web $w) {
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
    
    // Check that tag actually assigned
    $existing_tag_assign = TagService::getInstance($w)->getObject('TagAssign', ['object_class' => $class, 'object_id' => $id, 'tag_id' => $tag->id, 'is_deleted' => 0]);
    if (!empty($existing_tag_assign)) {
        $existing_tag_assign->delete();
    }
    
    $w->out('{}');
    
}