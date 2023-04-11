<?php

function index_ALL(Web &$w) {
    // $w->out(print_r(SearchService::getInstance($w)->getIndexes(),true));
    $w->ctx("indexes", SearchService::getInstance($w)->getIndexes());

    $tags = [];
    if (Config::get('tag.active') == true) {
        $tags = TagService::getInstance($w)->getAllTags(true);
    }
    $w->ctx("tags", $tags);

    if (Request::string("isbox") !== NULL) {
        $w->setLayout(null);
    }
}
