<?php

function index_ALL(Web $w)
{
    $w->setLayout('layout-bootstrap-5');
    ChannelsService::getInstance($w)->navigation($w, "Channels");
}
