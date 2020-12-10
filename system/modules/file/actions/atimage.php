<?php

function atimage_GET(Web $w)
{
    list($id) = $w->pathMatch();

    $attachment = FileService::getInstance($w)->getAttachment($id);
    if (empty($attachment) || !$attachment->exists() || !$attachment->isImage()) {
        header("HTTP/1.1 404 Not Found");
        return;
    }

    header("Content-Type: image/jpeg");

    if ($attachment->hasCachedImage()) {
        echo file_get_contents($attachment->getImageCachePath());
        return;
    }

    if (!$attachment->createCachedImage()) {
        header("HTTP/1.1 404 Not Found");
        return;
    }

    echo file_get_contents($attachment->getImageCachePath());
}
