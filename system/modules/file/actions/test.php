<?php

function test_GET(Web $w)
{
    $attachment = $w->File->getAttachment(1);
    if (empty($attachment)) {
        return;
    }
    $path = $w->File->cacheFileLocally($attachment->getFile());
}
