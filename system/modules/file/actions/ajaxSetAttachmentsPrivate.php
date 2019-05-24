<?php

function ajaxSetAttachmentsPrivate_POST(Web $w) {
    $attachment_ids = json_decode($_REQUEST);
    foreach ($attachment_ids as $id) {
        $attachment = $w->File->getAttchamnet($id);
        $attachment->is_private = 0;
        $attachment->update();
    }
}