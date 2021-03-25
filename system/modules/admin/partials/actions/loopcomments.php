<?php

namespace System\Modules\Admin;

function loopcomments(\Web $w, $params)
{
    $w->ctx("comments", $params['object']);
    $w->ctx("redirect", $params['redirect']);

    $internal_only = array_key_exists('internal_only', $params) ? $params['internal_only'] : true;
    $w->ctx("internal_only", $internal_only);
    $w->ctx("external_only", $internal_only === true ? false : (array_key_exists('external_only', $params) ? $params['external_only'] : false));
}
