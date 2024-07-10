<?php

function listchannels_GET(Web $w)
{
    $w->setLayout('layout-bootstrap-5');
    ChannelsService::getInstance($w)->navigation($w, "Channels List");
    History::add('List Channels');

    // Get known channel types: email and web
    $email_channels = ChannelService::getInstance($w)->getEmailChannels() ?? [];
    $web_channels = ChannelService::getInstance($w)->getWebChannels() ?? [];

    $w->ctx("channels", array_merge($email_channels, $web_channels));
}
