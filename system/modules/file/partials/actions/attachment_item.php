<?php

namespace System\Modules\File;

function attachment_item_GET(\Web $w, $params)
{
    $w->ctx("attachment", $params["attachment"]);
    $w->ctx("owner", \RestrictableService::getInstance($w)->getOwner($params["attachment"]));
    $w->ctx("redirect", $params["redirect"]);
}
