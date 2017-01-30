<?php

function delete_GET(Web $w) {
    $p = $w->pathMatch("id");
    if (empty($p["id"])) {
        $w->error(__("No connection ID specified"), "/report-connections");
    }
    
    $connection = $w->Report->getConnection($p["id"]);
    if (empty($connection->id)) {
        $w->error(__("Connection could not be found"), "/report-connections");
    }
    
    $connection->delete();
    $w->msg(__("Connection deleted"), "/report-connections");
}
