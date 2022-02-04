<?php

function ajaxAddAttachment_POST(Web $w)
{
    $w->setLayout(null);

    $request_data = json_decode($_POST["file_data"]);
    if (empty($request_data)) {
        $w->out((new AxiosResponse())->setErrorResponse(null, ["error_message" => "Missing attachment data"]));
        return;
    }

    $user = AuthService::getInstance($w)->user();
    if (isset($request_data->is_restricted) && $request_data->is_restricted && !$user->hasRole("restrict")) {
        $w->out((new AxiosResponse())->setErrorResponse(null, ["error_message" => "User not authorised to restrict objects"]));
        return;
    }

    $object = FileService::getInstance($w)->getObject($request_data->class, $request_data->class_id);
    if (empty($object)) {
        $w->out((new AxiosResponse())->setErrorResponse(null, ["error_message" => "Missing attachment class"]));
        return;
    }

    $title = property_exists($request_data, "title") ? $request_data->title : null;
    $description = property_exists($request_data, "description") ? $request_data->description : null;
    $type_code = property_exists($request_data, "type_code") ? $request_data->type_code : null;
    $is_public = property_exists($request_data, "is_public") ? $request_data->is_public : false;

    $attachment_id = FileService::getInstance($w)->uploadAttachment("file", $object, $title, $description, $type_code, $is_public);
    if (empty($attachment_id)) {
        $w->out((new AxiosResponse())->setErrorResponse(null, ["error_message" => "Failed to add attachment"]));
        return;
    }

    $attachment = FileService::getInstance($w)->getAttachment($attachment_id);

    if (isset($request_data->is_restricted) && $request_data->is_restricted) {
        RestrictableService::getInstance($w)->setOwner($attachment, $user->id);

        foreach (!empty($request_data->viewers) ? $request_data->viewers : [] as $viewer) {
            RestrictableService::getInstance($w)->addViewer($attachment, $viewer->id);
        }
    }

    $w->out((new AxiosResponse())->setSuccessfulResponse("OK", [
        "attachment_id" => $attachment_id,
        "attachment_name" => $attachment->title,
        "attachment_description" => $attachment->description,
        "attachment_url" => FileService::getInstance($w)->isImage($attachment->fullpath) ? WEBROOT . '/file/atfile/' . $attachment->id : WEBROOT . '/file/atfile/' . $attachment->id . "/" . $attachment->filename
    ]));
}
