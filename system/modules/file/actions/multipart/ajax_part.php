<?php

/**
 * Receive a presigned UploadPart url for multipart uploads
 */
function ajax_part_POST(Web $w)
{
    $w->setLayout(null);

    $body = json_decode(file_get_contents('php://input'), true);

    $upload_id = $body["id"];
    $partNumber = $body["part"];
    $length = $body["length"];
    $md5 = $body["md5"];

    // TODO: check if user has permission to upload to this

    $obj = FileMultipartUploadService::getInstance($w)
        ->getObject("FileS3Object", $upload_id);

    $presigned = FileMultipartUploadService::getInstance($w)
        ->getPresignedUploadPart($obj, $partNumber, $length, $md5);

    $w->out(json_encode(["endpoint" => $presigned]));
}
