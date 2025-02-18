<?php

/**
 * Start a multipart upload for bucket defined in file.adapters.s3.bucket
 * If you need to change parameters such as s3 bucket/key/etc,
 * you should make a new endpoint in your module that accepts your own values
 */
function ajax_multipart_POST(Web $w)
{
    $w->setLayout(null);

    $body = json_decode(file_get_contents('php://input'), true);

    $filename = $body["filename"];
    $prefix = Config::get("file.adapters.s3.options.directory");
    $key = $prefix . "/" . hash("md5", $filename);

    $mime = $body["mime"];

    $upload = FileMultipartUploadService::getInstance($w)
        ->startMultipart(
            $key,
            $mime,
            Config::get("file.adapters.s3.bucket")
        );

    $w->out(json_encode(["id" => $upload->id]));
}

/**
 * Abort a multipart upload
 */
function ajax_multipart_DELETE(Web $w)
{
    $w->setLayout(null);

    [$upload_id] = $w->pathMatch("id");

    $obj = FileMultipartUploadService::getInstance($w)
        ->getObject("FileS3Object", $upload_id);

    FileMultipartUploadService::getInstance($w)
        ->abortMultipart($obj);

    $obj->delete();

    $w->out((new JsonResponse())->setStatus(200));
}
