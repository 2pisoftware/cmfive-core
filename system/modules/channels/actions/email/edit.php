<?php

function edit_GET(Web $w)
{
    $w->setLayout('layout-bootstrap-5');
    $p = $w->pathMatch("id");
    $channel_id = $p["id"];

    ChannelsService::getInstance($w)->navigation($w, $channel_id ? "Edit" : "Add" . " an Email Channel");

    // Get channel and form
    $channel_object = $channel_id ? ChannelService::getInstance($w)->getChannel($channel_id) : new Channel($w);
    $form = $channel_object->getForm();

    $email_channel = $channel_id ? ChannelService::getInstance($w)->getEmailChannel($channel_id) : new EmailChannelOption($w);

    // Decrypt username and password
    $email_channel->decrypt();

    $form["Email"] = [
        [
            ["Protocol", "select", "protocol", StringSanitiser::sanitise($email_channel->protocol), $email_channel::$_select_protocol]
        ],
        [
            ["Server URL", "text", "server", StringSanitiser::sanitise($email_channel->server)]
        ],
        [
            ["Username", "text", "s_username", $email_channel->s_username],
            ["Password", "password", "s_password", $email_channel->s_password]
        ],
        [
            ["Port <small>Only required for non-standard port configuarations</small>", "text", "port", StringSanitiser::sanitise($email_channel->port)],
            ["Use Auth?", "checkbox", "use_auth", intval($email_channel->use_auth)]
        ],
        [
            ['Verify Peer', 'checkbox', 'verify_peer', $email_channel->verify_peer == null ? 0 : intval($email_channel->verify_peer)],
            ['Allow self signed certificates', 'checkbox', 'allow_self_signed', $email_channel->allow_self_signed == null ? 0 : intval($email_channel->allow_self_signed)]
        ],
        [
            ["Folder", "text", "folder", StringSanitiser::sanitise($email_channel->folder)]
        ],
    ];

    $form["Filter"] = [
        [
            ["To", "text", "to_filter", StringSanitiser::sanitise($email_channel->to_filter)],
            ["From", "text", "from_filter", StringSanitiser::sanitise($email_channel->from_filter)]
        ],
        [
            ["CC", "text", "cc_filter", StringSanitiser::sanitise($email_channel->cc_filter)],
            ["Subject", "text", "subject_filter", StringSanitiser::sanitise($email_channel->subject_filter)]
        ],
        [
            ["Body", "text", "body_filter", StringSanitiser::sanitise($email_channel->body_filter)]
        ]
    ];

    $form["Action"] = [
        [
            ["Post Read Action", "select", "post_read_action", StringSanitiser::sanitise($email_channel->post_read_action), $email_channel::$_select_read_action],
            ["Post Read Data", "text", "post_read_parameter", StringSanitiser::sanitise($email_channel->post_read_parameter)]
        ]
    ];

    $validation = [
        'name' => ['required'],
        'protocol' => ['required'],
        'server' => ['required'],
        's_username' => ['required'],
        's_password' => ['required']
    ];

    $w->ctx("form", HtmlBootstrap5::multiColForm($form, "/channels-email/edit/{$channel_id}", "POST", "Save", "channelform", null, null, '_self', true, $validation));
}

function edit_POST(Web $w)
{
    $p = $w->pathMatch("id");
    $channel_id = $p["id"];
    $channel_object = $channel_id ? ChannelService::getInstance($w)->getChannel($channel_id) : new Channel($w);
    $channel_object->fill($_POST);
    $channel_object->is_active = isset($_POST['is_active']) ? 1 : 0;
    $channel_object->notify_user_id = isset($_POST["notify_user_id"]) ? intval($_POST["notify_user_id"]) : null;
    $channel_object->do_processing = isset($_POST['do_processing']) ? 1 : 0;
    $channel_object->insertOrUpdate();

    $email_channel = $channel_id ? ChannelService::getInstance($w)->getEmailChannel($channel_id) : new EmailChannelOption($w);
    $email_channel->fill($_POST);
    $email_channel->use_auth = isset($_POST['use_auth']) ? 1 : 0;
    $email_channel->verify_peer = isset($_POST['verify_peer']) ? 1 : 0;
    $email_channel->allow_self_signed = isset($_POST['allow_self_signed']) ? 1 : 0;
    $email_channel->port = isset($_POST['port']) ? intval($_POST['port']) : null;
    $email_channel->channel_id = $channel_object->id;
    $email_channel->insertOrUpdate();

    $w->msg("Email Channel " . ($channel_id ? "updated" : "created"), "/channels/listchannels");
}
