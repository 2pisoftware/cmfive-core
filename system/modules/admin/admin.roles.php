<?php
function role_comment_allowed(Web $w,$path) {
    return $w->checkUrl($path, "admin", null, "comment") ||
           $w->checkUrl($path, "admin", null, "deletecomment") ||
           $w->checkUrl($path, "admin", null, "ajaxSaveComment") ||
           $w->checkUrl($path, "admin", null, "ajaxAddComment");
}
