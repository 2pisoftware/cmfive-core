<?php

function edit_GET(Web $w)
{
    $w->setLayout('layout-bootstrap-5');
    $p = $w->pathMatch("id");
    $channel_id = $p["id"];

    ChannelsService::getInstance($w)->navigation($w, $channel_id ? "Edit" : "Add" . " a Web Channel");

    // Get channel and form
    $channel_object = $channel_id ? ChannelService::getInstance($w)->getChannel($channel_id) : new Channel($w);
    $form = $channel_object->getForm();

    $web_channel = $channel_id ? ChannelService::getInstance($w)->getWebChannel($channel_id) : new WebChannelOption($w);
    if (empty($web_channel)) {
        $web_channel = new WebChannelOption($w);
    }

    $form["Web"] = [
        [
            [
                "Web API URL",
                "text",
                "url",
                $web_channel->url
            ]
        ],
    ];

    $w->ctx("form", HtmlBootstrap5::multiColForm($form, "/channels-web/edit/{$channel_id}", "POST", "Save", "channelform"));
}

function edit_POST(Web $w)
{
    $p = $w->pathMatch("id");
    $channel_id = $p["id"];

    $channel_object = $channel_id ? ChannelService::getInstance($w)->getChannel($channel_id) : new Channel($w);
    $channel_object->fill($_POST);
    $channel_object->notify_user_id = !empty($_POST["notify_user_id"]) ? intval($_POST["notify_user_id"]) : null;
    $channel_object->is_active = !empty($_POST["is_active"]) ? intval($_POST["is_active"]) : 0;
    $channel_object->do_processing = !empty($_POST["do_processing"]) ? intval($_POST["do_processing"]) : 0;
    $channel_object->insertOrUpdate();

    $web_channel = $channel_id ? ChannelService::getInstance($w)->getWebChannel($channel_id) : new WebChannelOption($w);
    $web_channel->fill($_POST);
    $web_channel->channel_id = $channel_object->id;
    $web_channel->insertOrUpdate();

    $w->msg("Web Channel " . ($channel_id ? "updated" : "created"), "/channels/listchannels");
}
