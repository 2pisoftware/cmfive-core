<?php

function delete_ALL(Web $w) {
    $p = $w->pathMatch("id");

    if (!empty($p["id"])){
        $tag = TagService::getInstance($w)->getTag($p["id"]);
        if (!empty($tag->id)) {
            $tag->delete();
            $w->msg("Tag deleted", "/tag/admin");
        }
    }
    
    $w->error("Could not find tag", "/tag/admin");

}
