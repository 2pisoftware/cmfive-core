<?php

use Html\Cmfive\QuillEditor;
use Html\Form\InputField\Time;

function starttimer_GET(Web $w)
{
    $w->setLayout(null);
    $w->out(HtmlBootstrap5::multiColForm([
        "Start Timer" => [
            [
                new QuillEditor([
                    "label|id" => "Description",
                ])
            ],
            [
                new Time([
                    "label" => "Start Time (default: now)",
                    "id|name" => "start_time",
                    "class" => "form-control",
                    "pattern" => "^(0?[0-9]|1[0-9]|2[0-3]):[0-5][0-9](\s+)?(AM|PM|am|pm)?$",
                    "placeholder" => "12hr format: 11:30pm or 24hr format: 23:30",
                ])
            ]
        ]
    ], "javascript:window.timelog_startTimer()", null, "Save", "timelog_starttimer_modal", null, null, "_self", true, null, false));
}
