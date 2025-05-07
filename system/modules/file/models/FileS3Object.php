<?php

class FileS3Object extends DbObject
{
    /**
     * The display name of this file provided by the user.
     * @var string | null
     */
    public $display_name;

    /**
     * The UploadId provided by aws when starting the multipart upload
     * @var string
     */
    public $upload_id;

    /**
     * The s3 bucket of this upload
     * @var string
     */
    public $bucket;

    /**
     * The s3 key of this upload
     * 
     * 'key' is reserved word in sql
     */
    public $key_path;

    /**
     * The mime content type of the uploaded file
     * @var string
     */
    public $mime;

    /**
     * The table to attribute this upload to.
     * Once completed, a new Attachment will be made for this parent table
     * 
     * @var string
     */
    public $parent_table;

    /**
     * @var string
     */
    public $parent_id;
}
