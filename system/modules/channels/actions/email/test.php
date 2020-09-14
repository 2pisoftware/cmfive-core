<?php
function test_GET(Web $w)
{
    $p = $w->pathMatch("id");
    $id = $p["id"];

    if ($id) {
        $channel = ChannelService::getInstance($w)->getEmailChannel($id);
        $result = $channel->connectToMail(true)[1];
        if (gettype($result) == 'string') {
            echo $result;
        } else {
            echo 'Connected!';
        }
    } else {
        $w->error("Could not find channel");
    }
}
