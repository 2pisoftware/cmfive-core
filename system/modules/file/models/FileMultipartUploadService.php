<?php

use Aws\S3\S3Client;

/**
 * for each file:
 * - client sends req to /start with file details, gets back presigned multipart command and upload id
 * - uploads file to that endpoint
 * - client sends req to /stop with unique id
 *
 * - server checks each upload id on s3 if they match the provided file details
 */

class FileMultipartUploadService extends DbService
{
    /**
     * Start a multipart upload
     *
     * @param DbObject|null $parent
     */
    public function startMultipart(
        string $key,
        string $mime,
        string|null $bucket,
        $parent = null,
    ) {
        if (empty($bucket)) {
            $bucket = Config::get("file.adapters.s3.bucket");
        }

        $client = $this->makeClient();

        $ret = $client->createMultipartUpload([
            "Bucket" => $bucket,
            "ContentType" => $mime,
            "Key" => $key,
        ]);

        $obj = new FileS3Object($this->w);
        $obj->upload_id = $ret["UploadId"];
        $obj->bucket = $bucket;
        $obj->key_path = $key;
        $obj->mime = $mime;

        if (!empty($parent)) {
            $obj->parent_id = $parent->id;
            $obj->parent_table = $parent->getDbTableName();
        }

        $obj->insert();

        return $obj;
    }

    public function getPresignedUploadPart(FileS3OBject $obj, int $part, int $length, string $md5)
    {
        $client = $this->makeClient();

        $command = $client->getCommand("UploadPart", [
            "Bucket" => $obj->bucket,
            "Key" => $obj->key_path,
            "ContentMD5" => $md5,
            "ContentLength" => $length,
            "PartNumber" => $part,
            "UploadId" => $obj->upload_id,
        ]);

        $presigned = $client->createPresignedRequest($command, strtotime("60 minutes"));

        return strval($presigned->getUri());
    }

    /**
     * Complete a multipart upload. If $parent is provided, also create an Attachment.
     *
     * @param DbObject | null $parent
     */
    public function finishMultipart(FileS3Object $obj)
    {
        $client = $this->makeClient();

        ["Parts" => $parts] = $client->listParts([
            "Bucket" => $obj->bucket,
            "Key" => $obj->key_path,
            "UploadId" => $obj->upload_id,
        ]);

        $client->completeMultipartUpload([
            "Bucket" => $obj->bucket,
            "Key" => $obj->key_path,
            "UploadId" => $obj->upload_id,
            "MultipartUpload" => [
                "Parts" => $parts,
            ]
        ]);

        if (!empty($obj->parent_table) && !empty($obj->parent_id)) {
            $existing = FileService::getInstance($this->w)
                ->getObject("Attachment", ["fullpath" => $obj->key_path]);
            if (!empty($existing)) {
                return $existing;
            }

            $attachment = new Attachment($this->w);
            $attachment->parent_table = $obj->parent_table;
            $attachment->parent_id = $obj->parent_id;
            $attachment->filename = substr($obj->key_path, strpos($obj->key_path, "/") + 1);
            $attachment->adapter = "s3";
            $attachment->fullpath = $obj->key_path;
            $attachment->skip_path_prefix = true;
            $attachment->mimetype = $obj->mime;
            $attachment->insert();

            return $attachment;
        }

        return true;
    }

    public function abortMultipart(FileS3Object $obj)
    {
        $client = $this->makeClient();

        $client->abortMultipartUpload([
            "Bucket" => $obj->bucket,
            "Key" => $obj->key_path,
            "UploadId" => $obj->upload_id,
        ]);

        $obj->delete();
    }

    /**
     * Delete objects on s3 matching some filter
     * Must provide at least one of prefix, regex
     * On success, return true. Otherwise throw.
     */
    public function deleteMatching(string $bucket, string $prefix = null, string $regex = null)
    {
        $client = $this->makeClient();

        $client->deleteMatchingObjects($bucket, $prefix, $regex);

        return true;
    }

    private function makeClient()
    {
        return FileService::getInstance($this->w)->getS3Client();
    }
}
