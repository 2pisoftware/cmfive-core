<?php

function deletecomment_ALL(Web &$w){
	$p = $w->pathMatch("id");
    $comment_id = intval($p["id"]);
    
    if (!empty($comment_id)){
    	$comment = $w->Comment->getComment($comment_id);
    	if (!empty($comment)){
    		$comment->delete();
    	}
    }

    $redirectUrl = $w->request("redirect_url");
    $w->msg(__("Comment deleted."), !empty($redirectUrl) ? $redirectUrl : $_SERVER["REQUEST_URI"]);
}
