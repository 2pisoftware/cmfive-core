<?php namespace System\Modules\Admin;

function loopcomments(\Web $w, $params) {
    $w->ctx("comments", $params['object']);
    $w->ctx("redirect", $params['redirect']);
}
    