<?php namespace System\Modules\Admin;

function loopcomments(\Web $w, $params) {
    $w->ctx("comments", $params['object']);
    $w->ctx("redirect", $params['redirect']);

    $internal_only = !empty($params['internal_only']) ? $params['internal_only'] : false;
    $w->ctx("internal_only", $internal_only);
    $w->ctx("external_only", $internal_only === true ? false : !empty($params['external_only']) ? $params['external_only'] : false);
}
    