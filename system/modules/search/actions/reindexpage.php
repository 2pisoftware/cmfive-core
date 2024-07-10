<?php //display all publishers games and website url
function reindexpage_ALL(Web $w) {

    $w->setLayout('layout-bootstrap-5');

    if (!AuthService::getInstance($w)->user()->is_admin) {
        $w->error("Access Restricted");
    }
    
    $w->ctx("title", "Search Admin");

}