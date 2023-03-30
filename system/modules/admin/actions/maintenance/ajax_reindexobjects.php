<?php

function ajax_reindexobjects_GET(Web $w)
{
    if (AuthService::getInstance($w)->user() != null && AuthService::getInstance($w)->user()->is_admin  == 1) {
        $w->setLayout(null);
        SearchService::getInstance($w)->reindexAll();

        echo $w->db->get('object_index')->count();
    } else {
        echo 0;
    }
}
