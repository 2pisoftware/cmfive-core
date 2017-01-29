<?php namespace System\Modules\Admin;

function displaycomment(\Web $w, $params) {
    if (!empty($params['redirect'])) {
        $w->ctx("redirect", $params['redirect']);
    }
    if (!empty($params['displayOnly'])) {
        $w->ctx("displayOnly", true);
    }
    $w->ctx("c", $params['object']);
}
