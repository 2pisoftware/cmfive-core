<?php

namespace System\Modules\File;

function listattachments(\Web $w, $params)
{
    $object = $params["object"];
    $redirect = $params["redirect"];

    if ($redirect[0] !== '/') {
        $redirect = '/' . $redirect;
    }

    $page = $w->sessionOrRequest("attachment__" . hash("crc32", get_class($object) . $object->id) . "__page", 1);
    $page_size = 6;
    $w->ctx("page", $page);
    $w->ctx("page_size", $page_size);

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

        $list_items[] = $w->partial("attachment_item", [
            "attachment" => $attachment,
            "redirect" => $redirect,
            "hide_image_exif" => $params["hide_image_exif"] ?? false,
            "hide_edit_restriction" => $params["hide_edit_restriction"] ?? false,
            "is_mutable" => $params["is_mutable"] ?? true,
        ], "file", "GET");
    }

    $w->ctx("list_items", $list_items);
    $w->ctx("redirect", $redirect);
    $w->ctx("object", $object);
    $w->ctx("hide_attach_btn", $params["hide_attach_btn"] ?? false);
}
