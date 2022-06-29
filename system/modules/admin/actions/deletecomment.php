<?php

function deletecomment_ALL(Web &$w)
{
    $p = $w->pathMatch("id");
    $comment_id = intval($p["id"]);

    if (!empty($comment_id)) {
        $comment = CommentService::getInstance($w)->getComment($comment_id);
        if (!empty($comment)) {
            $comment->delete();
        }
    }

    $w->msg("Comment deleted.", Request::string("redirect_url", $_SERVER["REQUEST_URI"]));
}
