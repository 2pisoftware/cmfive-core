<?php

function delete_GET(Web $w) {
    $p = $w->pathMatch("id");
    if (empty($p["id"])) {
        $w->error("No connection ID specified", "/report-connections");
    }
    
    $connection = $w->Report->getConnection($p["id"]);
    if (empty($connection->id)) {
        $w->error("Connection could not be found", "/report-connections");
    }
    
    $connection->delete();
    $w->msg("Connection deleted", "/report-connections");
}