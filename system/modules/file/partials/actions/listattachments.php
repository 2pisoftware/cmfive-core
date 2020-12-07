<?php

namespace System\Modules\File;

function listattachments(\Web $w, $params)
{
    $page = $w->sessionOrRequest("attachment__page", 1);
    $page_size = 6;
    $w->ctx("page", $page);
    $w->ctx("page_size", $page_size);

    $object = $params["object"];
    $redirect = $params["redirect"];

    $attachments = \FileService::getInstance($w)->getAttachments($object, !empty($object->id) ? $object->id : null, $page, $page_size);
    $list_items = [];

    foreach ($attachments as $attachment) {
        if (!$attachment->canView(\AuthService::getInstance($w)->user())) {
            continue;
        }

        if (stripos($attachment->filename, ".docx") || stripos($attachment->filename, ".doc") && !$attachment->is_public) {
            $attachment->dt_viewing_window = time();
            $attachment->update();
        }

        $list_items[] = $w->partial("attachment_item", ["attachment" => $attachment, "redirect" => $redirect], "file", "GET");
    }

    $w->ctx("list_items", $list_items);
    $w->ctx("redirect", $redirect);
    $w->ctx("object", $object);
}
