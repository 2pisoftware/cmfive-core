<?php

function index_ALL(Web &$w) {
    // $w->out(print_r(SearchService::getInstance($w)->getIndexes(),true));
    $indexes = SearchService::getInstance($w)->getIndexes();
    $selIndexes = [];
    foreach ($indexes as $k => $v) {
        $selIndexes[] = [$k,$v];
    }
    $w->ctx("indexes", $selIndexes);

    $tags = [];
    if (Config::get('tag.active') == true) {
        $tags = TagService::getInstance($w)->getAllTags(true);
    }
    $w->ctx("tags", $tags);

    if (Request::string("isbox") !== NULL) {
        $w->setLayout(null);
    }
}
