<?php

function ajaxSetAttachmentsPrivate_POST(Web $w) {
    $attachment_ids = ($_REQUEST['private_attachment_ids']);
    foreach ($attachment_ids as $id) {
        $attachment = $w->File->getAttachment($id);
        $attachment->is_public = 0;
        $attachment->update();
    }
}