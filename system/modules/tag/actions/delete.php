<?php

function delete_ALL(Web $w) {
    $p = $w->pathMatch("id");

    if (!empty($p["id"])){
        $tag = $w->Tag->getTag($p["id"]);
        if (!empty($tag->tag)) {
            $w->Tag->deleteTag($tag->tag);
            $w->msg("Tag deleted", "/tag");
        }
    }
    
    $w->error("Could not find tag", "/tag");

}
