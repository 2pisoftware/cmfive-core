<?php //display all publishers games and website url
function reindexpage_ALL(Web $w) {
    if (!$w->Auth->user()->is_admin) {
        $w->error("Access Restricted");
    }
    
    $w->ctx("title", "Search Admin");

}