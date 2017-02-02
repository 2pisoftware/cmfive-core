<?php namespace System\Modules\Admin;

function displaycomment(\Web $w, $params) {
    if (!empty($params['redirect'])) {
        $w->ctx("redirect", $params['redirect']);
    }
    if (!empty($params['displayOnly'])) {
        $w->ctx("displayOnly", true);
    }

    $w->ctx("internal_only", !empty($params['internal_only']) ? $params['internal_only'] : false);
    $w->ctx("external_only", !empty($params['external_only']) ? $params['external_only'] : false);

    $w->ctx("c", $params['object']);
}
