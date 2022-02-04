<?php

function ajaxStop_GET(Web $w) {
    $timelog = TimelogService::getInstance($w)->getActiveTimelogForUser();
    if (!empty($timelog->id)) {
        $timelog->stop();
    }
}