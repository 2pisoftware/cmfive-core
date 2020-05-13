<?php

function index_ALL(Web &$w) {
    // $w->out(print_r($w->Search->getIndexes(),true));
    $w->ctx("indexes", $w->Search->getIndexes());

    $tags = [];
    if (Config::get('tag.active') == true) {
        $tags = $w->Tag->getAllTags(true);
    }
    $w->ctx("tags", $tags);

    if ($w->request("isbox") !== NULL) {
        $w->setLayout(null);
    }
}
