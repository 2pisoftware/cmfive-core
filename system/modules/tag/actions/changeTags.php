<?php

function changeTags_GET(Web $w) {
    list($object_class, $id) = $w->pathMatch();

    if (empty($object_class) || empty($id)) {
        $w->ctx('error', 'Could not load taggable object');
        return;
    }

    $object = TagService::getInstance($w)->getObject($object_class, $id);
    if (empty($object->id)) {
        $w->ctx('error', 'Could not find object to tag');
        return;
    }

    $w->ctx('object', $object);
    $w->ctx('object_class', $object_class);
    $w->ctx('id', $id);

    $object_tags = TagService::getInstance($w)->getTagsByObjectClass($object_class);

    // Get current tags
    $w->ctx('tags', TagService::getInstance($w)->getTagsByObject($object));
    $w->ctx('object_tags', $object_tags);
    $w->ctx('all_tags', TagService::getInstance($w)->getTags());
}
