<?php

function index_ALL(Web $w)
{
    ChannelsService::getInstance($w)->navigation($w, "Channels");
}
