<?php

/**
 * Mark a multipart upload as done
 */
function ajax_done_POST(Web $w)
{
    $w->setLayout(null);

    [$upload_id] = $w->pathMatch("id");

    // TODO: check if user has permission to upload to this
    // they already must have file_upload to get to this route
    // but do they have permission to upload to this upload_id?

    $obj = FileMultipartUploadService::getInstance($w)
        ->getObject("FileS3Object", $upload_id);

    $attachment = FileMultipartUploadService::getInstance($w)
        ->finishMultipart($obj);

    $obj->delete();

    $w->callHook(
        "file",
        "multipart_upload_done",
        $attachment !== true
            ? [
                "attachment" => $attachment
            ]
            : null
    );

    $w->out(
        (new JsonResponse())
            ->setSuccessfulResponse(
                "Success",
                !empty($attachment->id) ? ["id" => $attachment->id] : []
            )
    );
}
