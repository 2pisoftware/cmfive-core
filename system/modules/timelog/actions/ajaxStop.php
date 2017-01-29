<?php

function ajaxStop_GET(Web $w) {
    $timelog = $w->Timelog->getActiveTimelogForUser();
    if (!empty($timelog->id)) {
        $timelog->stop();
    }
}