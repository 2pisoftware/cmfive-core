<?php

function listchannels_GET(Web $w)
{
    ChannelsService::getInstance($w)->navigation($w, "Channels List");

    // Get known channel types: email and web
    $email_channels = ChannelService::getInstance($w)->getEmailChannels() ?? [];
    $web_channels = ChannelService::getInstance($w)->getWebChannels() ?? [];

    $w->ctx("channels", array_merge($email_channels, $web_channels));
}
