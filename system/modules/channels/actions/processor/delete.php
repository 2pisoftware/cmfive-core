<?php

function delete_GET(Web $w)
{
    $p = $w->pathMatch("id");
    $id = $p["id"];

    if ($id) {
        $processor = ChannelService::getInstance($w)->getProcessor($id);
        $processor->delete();

        $w->msg("Processor deleted", "/channels/listprocessors");
    } else {
        $w->error("Could not find processor");
    }
}
