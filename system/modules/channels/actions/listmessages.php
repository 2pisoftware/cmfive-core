<?php

function listmessages_GET(Web $w)
{
    $w->setLayout('layout-bootstrap-5');
    ChannelsService::getInstance($w)->navigation($w, "Messages list");

    $p = $w->pathMatch("id");
    $channel_id = $p["id"];

    $messages = ChannelService::getInstance($w)->getMessages($channel_id);

    $w->ctx("messages", $messages);

    if ($channel_id) {
        $w->ctx("channel_id", $channel_id);
    }
}
