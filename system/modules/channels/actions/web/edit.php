<?php

function edit_GET(Web $w) {

    $p = $w->pathMatch("id");
    $channel_id = $p["id"];

    $w->Channels->navigation($w, $channel_id ? "Edit" : "Add" . " a Web Channel");

    // Get channel and form
    $channel_object = $channel_id ? $w->Channel->getChannel($channel_id) : new Channel($w);
    $form = $channel_object->getForm();

    $web_channel = $channel_id ? $w->Channel->getWebChannel($channel_id) : new WebChannelOption($w);

    $form["Web"] = array(
        array(
            array("Web API URL", "text", "url", $web_channel->url)
        ),
    );

    $w->ctx("form", Html::multiColForm($form, "/channels-web/edit/{$channel_id}", "POST", "Save", "channelform"));
}

function edit_POST(Web $w) {
    $p = $w->pathMatch("id");
    $channel_id = $p["id"];

    $channel_object = $channel_id ? $w->Channel->getChannel($channel_id) : new Channel($w);
    $channel_object->fill($_POST);
    $channel_object->notify_user_id = !empty($_POST["notify_user_id"]) ? intval($_POST["notify_user_id"]) : NULL;
    $channel_object->insertOrUpdate();

    $web_channel = $channel_id ? $w->Channel->getWebChannel($channel_id) : new WebChannelOption($w);
    $web_channel->fill($_POST);
    $web_channel->channel_id = $channel_object->id;
    $web_channel->insertOrUpdate();

    $w->msg("Web Channel " . ($channel_id ? "updated" : "created"), "/channels/listchannels");
}
