<?php

function view_GET(Web $w) {
    $redirect_url = Request::string("redirect_url");
    [$attachment_id] = $w->pathMatch("id");

    $attachment = FileService::getInstance($w)->getAttachment($attachment_id);
    if (empty($attachment)) {
        $w->error("Attachment not found", $redirect_url);
    }

    $owner = RestrictableService::getInstance($w)->getOwner($attachment);

    // how are you *meant* to get a user?
    $creator = AuthService::getInstance($w)->getUser($attachment->creator_id);

    $w->ctx("attachment", $attachment);
    $w->ctx("owner", $owner);
    $w->ctx("creator", $creator);
}