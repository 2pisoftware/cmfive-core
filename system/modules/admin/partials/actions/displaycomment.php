<?php namespace System\Modules\Admin;

function displaycomment(\Web $w, $params) {
    if (!empty($params['redirect'])) {
        $w->ctx("redirect", $params['redirect']);
    }
    if (!empty($params['displayOnly'])) {
        $w->ctx("displayOnly", true);
    }

    $w->ctx("internal_only", array_key_exists('internal_only', $params) ? $params['internal_only'] : true);
    $w->ctx("external_only", array_key_exists('external_only', $params) ? $params['external_only'] : false);

    $w->ctx("c", $params['object']);
    $w->ctx("is_outgoing", $params["is_outgoing"]);
}
