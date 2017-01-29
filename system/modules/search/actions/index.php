<?php

function index_ALL(Web &$w) {
    // $w->out(print_r($w->Search->getIndexes(),true));
    $w->ctx("indexes", $w->Search->getIndexes());
    $w->ctx("tags", $w->Tag->getAllTags(true));
    if ($w->request("isbox") !== NULL) {
        $w->setLayout(null);
    }
}
