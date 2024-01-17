<?php

function listprocessors_GET(Web $w)
{
    $w->setLayout('layout-bootstrap-5');
    ChannelsService::getInstance($w)->navigation($w, "Processors List");
    // Get all email, FTP, local processors
    $processors = ChannelService::getInstance($w)->getAllProcessors();

    $w->ctx("processors", $processors);
}
