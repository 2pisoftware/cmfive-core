<?php

function deletecomment_ALL(Web &$w)
{
    list($comment_id) = $w->pathMatch("id");

    if (!empty($comment_id)) {
        $comment = CommentService::getInstance($w)->getComment($comment_id);
        if (!empty($comment)) {
            $comment->delete();
        }
    }

    $w->msg("Comment deleted.", Request::string("redirect_url", $_SERVER["REQUEST_URI"]));
}
