<?php

function process_GET(Web $w)
{
    $w->setLayout(null);
    $p = $w->pathMatch("id");
    $id = $p["id"];

    if ($id) {
        $processors = ChannelService::getInstance($w)->getProcessors($id);
        if (!empty($processors)) {
            foreach ($processors as $processor) {
                $processor_class = $processor->retrieveProcessor();
                $processor_class->process($processor);
            }

            ChannelService::getInstance($w)->markMessagesAsProcessed($id);
        }
    } else {
        $w->out("No channel found.");
    }
}
