<?php

namespace System\Modules\File;

function attachment_item_GET(\Web $w, $params)
{
    $w->ctx("attachment", $params["attachment"]);
    $w->ctx("owner", \RestrictableService::getInstance($w)->getOwner($params["attachment"]));
    $w->ctx("redirect", $params["redirect"]);
    $w->ctx("image_data_blocked", $params["hide_image_exif"] ?? false);

}
