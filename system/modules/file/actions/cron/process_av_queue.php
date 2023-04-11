<?php

function process_av_queue_GET(Web $w)
{
    $w->setLayout(null);
    AntivirusService::getInstance($w)->processQueue();
}
