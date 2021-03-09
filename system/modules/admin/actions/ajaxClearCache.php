<?php

function ajaxClearCache_GET(Web $w)
{
    if (AuthService::getInstance($w)->user() === null || !AuthService::getInstance($w)->user()->is_admin) {
        return;
    }

    if (is_file(ROOT_PATH . '/cache/classdirectory.cache')) {
        unlink(ROOT_PATH . '/cache/classdirectory.cache');
    }
    if (is_file(ROOT_PATH . '/cache/config.cache')) {
        unlink(ROOT_PATH . '/cache/config.cache');
    }
}
