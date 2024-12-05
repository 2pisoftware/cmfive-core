<?php

use Html\Form\InputField\Text;

function edit_GET(Web $w) {
    $p = $w->pathMatch("id");
    
    $t = TagService::getInstance($w)->getTag($p['id']);
    $newForm = [
        "Edit Tag" => [
            [
                new Text([
                    "label" => "Name",
                    "id|name" => "tag",
                    "value" => $t->tag,
                ])
            ]
        ]
    ];

    $w->ctx("edittagform", HtmlBootstrap5::multiColForm($newForm, $w->localUrl('/tag/edit/'.$t->id)));
}

function edit_POST(Web $w) {
    $p = $w->pathMatch("id");
    
    $existing_tag = TagService::getInstance($w)->getObject("Tag", ['tag' => trim(strip_tags(Request::string('tag'))), 'is_deleted' => 0]);
    if (!empty($existing_tag)) {
        $w->error("Tag named '" . Request::string('tag') . "' already exists.", '/tag/edit/' . $p['id']);
    }

    $tag = TagService::getInstance($w)->getTag($p['id']);
    $tag->tag = trim(strip_tags(Request::string('tag')));
    $tag->update();
    
    $w->msg("Tag saved", "/tag/admin");
}