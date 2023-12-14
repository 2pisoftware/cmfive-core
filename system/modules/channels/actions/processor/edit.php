<?php

function edit_GET(Web $w)
{
    $p = $w->pathMatch("id");
    $processor_id = $p["id"];

    ChannelsService::getInstance($w)->navigation($w, $processor_id ? "Edit" : "Add" . " a Processor");

    // Get channel and form
    $processor = $processor_id ? ChannelService::getInstance($w)->getProcessor($processor_id) : new ChannelProcessor($w);
    $processor_list = ChannelService::getInstance($w)->getProcessorList();

    $form = [
        "Processor" => [
            [
                ["Name", "text", "name", $processor->name]
            ],
            [
                ["Channel", "select", "channel_id", $processor->channel_id, ChannelService::getInstance($w)->getChannels()]
            ],
            [
                ["Processor Class", "select", "processor_class", $processor->module . '.' . $processor->class, $processor_list]
            ]
        ]
    ];

    $validation = [
        'name' => ['required'],
        'channel_id' => ['required'],
        'processor_class' => ['required']
    ];

    $w->out(HtmlBootstrap5::multiColForm($form, "/channels-processor/edit/{$processor_id}", "POST", "Save", "processor_form", null, null, '_self', true, $validation));
}

function edit_POST(Web $w)
{
    $p = $w->pathMatch("id");
    $processor_id = $p["id"];

    // Break the selected processor up into module and class
    $processor_class = Request::string("processor_class");
    $processor_expl = explode(".", $processor_class);

    // Make sure we only have two values
    if (count($processor_expl) !== 2) {
        $w->error("Missing Processor values", "/channels/listprocessors");
        exit();
    }

    // make sure the selected class exists in config
    if (!in_array($processor_expl[1], $w->moduleConf($processor_expl[0], "processors"))) {
        $w->error("Could not find processor in config", "/channels/listprocessors");
        exit();
    }

    $processor_object = $processor_id ? ChannelService::getInstance($w)->getProcessor($processor_id) : new ChannelProcessor($w);
    $processor_object->fill($_POST);
    $processor_object->channel_id = Request::int("channel_id");
    $processor_object->module = $processor_expl[0];
    $processor_object->class = $processor_expl[1];
    $processor_object->insertOrUpdate();

    $w->msg("Processor " . ($processor_id ? "updated" : "created"), "/channels/listprocessors");
}
