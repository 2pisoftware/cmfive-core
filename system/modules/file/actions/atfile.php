<?php
function atfile_GET(Web &$w)
{
    $w->setLayout(null);
    list($id) = $w->pathMatch();

    $attachment = FileService::getInstance($w)->getAttachment($id);
    if (!empty($attachment) && $attachment->exists()) {
        //check if no user logged in, is attachment public
        if (!AuthService::getInstance($w)->loggedIn() && !($attachment->is_public || $attachment->checkViewingWindow())) {
            return;
        }
        $attachment->displayContent();
    } else {
        $w->header("HTTP/1.1 404 Not Found");
        $w->notFoundPage();
    }
}
