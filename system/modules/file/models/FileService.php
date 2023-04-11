<?php

// use Gaufrette\Filesystem;
use Gaufrette\File as File;
// use Gaufrette\Adapter\Local as LocalAdapter;
// use Gaufrette\Adapter\InMemory as InMemoryAdapter;
// use Gaufrette\Adapter\AwsS3 as AwsS3;
use Aws\S3\S3Client as S3Client;
// use Gaufrette\StreamWrapper as StreamWrapper;

/**
 * Service class with functions to help managing files and attachment records.
 * Encapsulates the Gaufrette library of file system adapters.
 */
class FileService extends DbService
{
    private static $cache_runtime_path;

    public static $_thumb_height = 200;
    public static $_thumb_width = 200;
    public static $_stream_name = "attachment";

    /**
     * Returns the max upload file size in bytes
     *
     * @return int $fileSize;
     */
    public function getMaxFileUploadSize()
    {
        $ini_upload_limit = ini_get('upload_max_filesize');

        $units = 'B';
        if (intval($ini_upload_limit) !== $ini_upload_limit) {
            $units = substr($ini_upload_limit, -1);
            $ini_upload_limit = substr($ini_upload_limit, 0, strlen($ini_upload_limit) - 1);
        }

        $multiplier = 1;
        switch (strtoupper($units)) {
            case "G":
                $multiplier *= 1024;
                // Fallthrough.
            case "M":
                $multiplier *= 1024;
                // Fallthrough.
            case "K":
                $multiplier *= 1024;
                break;
        }

        return (int) $ini_upload_limit * $multiplier;
    }

    /**
     * Create a new Gaufrette File object from a filename and path
     *
     * @param \Gaufrette\Filesystem
     * @param string filename
     *
     * @return \Gaufrette\File
     */
    public function getFileObject($filesystem, $filename)
    {
        return new File($filename, $filesystem);
    }

    /**
     * Returns the first adapter marked as active that isn't "local".
     *
     * The local filesystem is the default adapter if none are specified.
     *
     * @return string adapter
     */
    public function getActiveAdapter()
    {
        $adapters = Config::get('file.adapters');
        if (!empty($adapters)) {
            // Omit local because we always default to it
            foreach ($adapters as $adapter => $settings) {
                if ($settings['active'] == true && $adapter !== "local") {
                    return $adapter;
                }
            }
        }

        // Always default to local
        return "local";
    }

    /**
     * Get a Gaufrette Filesystem for the currently active adapter and selected path
     *
     * @param mixed $path base path to load the filesystem adapter at
     * @param mixed $content content to load into the filesystem (mainly used for the "memory" adapter
     * @param array $options options to give to the filesystem
     *
     * @return League\Flysystem\Filesystem
     */
    public function getFilesystem($path = null, $content = null, $options = [])
    {
        return $this->getSpecificFilesystem($this->getActiveAdapter(), $path, $content, $options);
    }

    /**
     * Get a Gaufrette Filesystem for a given adapter and path
     *
     * @param string $adapter adapter to load
     * @param string $path base path to load the filesystem adapter at
     * @param mixed $content content to load into the filesystem (mainly used for the "memory" adapter
     * @param array $options options to give to the filesystem
     *
     * @return League\Flysystem\Filesystem
     */
    public function getSpecificFilesystem($adapter = "local", $path = null, $content = null, $options = [])
    {
        $adapter_obj = null;
        switch ($adapter) {
            case "local":
                $adapter_obj = new League\Flysystem\Local\LocalFilesystemAdapter($path);
                break;
            case "memory":
                $adapter_obj = new League\Flysystem\InMemory\InMemoryFilesystemAdapter();
                break;
            case "s3":
                $client = $this->getS3ClientBelowFilesystem();
                // $config_options = Config::get('file.adapters.s3.options');
                $s3path = (substr($path, -1) == "/") ? substr($path, 0, -1) : $path; // because trailing presence varies with call/object history
                // $config_options = array_replace(is_array($config_options) ? $config_options : [], ["directory" => $s3path], $options);
                $adapter_obj = new League\Flysystem\AwsS3V3\AwsS3V3Adapter($client, Config::get('file.adapters.s3.bucket'), $s3path); // , $s3path, is_array($config_options) ? $config_options : []);
                break;
        }

        if ($adapter_obj !== null) {
            return new League\Flysystem\Filesystem($adapter_obj);
        }
        return null;
    }

    /**
     * Get a Gaufrette Filesystem for a given adapter, adapter config and path
     *
     * @param string $adapter adapter to load
     * @param array $adapter_config to use
     * @param string $path base path to load the filesystem adapter at
     * @param mixed $content content to load into the filesystem (mainly used for the "memory" adapter
     * @param array $options options to give to the filesystem
     *
     * @return League\Flysystem\Filesystem
     */
    public function getSpecificFilesystemWithCustomAdapter($adapter = 'local', $adapter_config = null, $path = null, $content = null, $options = [])
    {
        $adapter_obj = null;
        switch ($adapter) {
            case "local":
                $adapter_obj = new League\Flysystem\Local\LocalFilesystemAdapter($path);
                break;
            case "memory":
                $adapter_obj = new League\Flysystem\InMemory\InMemoryFilesystemAdapter();
                break;
            case "s3":
                $config_options = $adapter_config['options'];
                $config_options = array_replace(is_array($config_options) ? $config_options : [], ["directory" => $path], $options);

                $client = $this->getS3ClientBelowFilesystem();
                $adapter_obj = new AwsS3($client, $adapter_config['bucket'], is_array($config_options) ? $config_options : []);
                break;
        }

        if ($adapter_obj !== null) {
            return new League\Flysystem\Filesystem($adapter_obj);
        }
        return null;
    }

    /**
     * 
     */
    public function getS3Client(): Aws\S3\S3Client
    {
        $args = [
            "region" =>  Config::get("file.adapters.s3.region", "ap-southeast-2"),
            "version" => Config::get("file.adapters.s3.version", "2006-03-01"),
        ];

        if (Config::get("system.environment", ENVIRONMENT_PRODUCTION) === ENVIRONMENT_DEVELOPMENT) {
            $args["credentials"] = Config::get("file.adapters.s3.credentials");
        }

        return new Aws\S3\S3Client($args);
    }

    /**
     * Register a gaufrette stream wrapper
     *
     * @param \Gaufrette\Filesystem
     *
     * @return void
     */
    public function registerStreamWrapper($filesystem = null)
    {
        if (!empty($filesystem)) {
            $map = \Gaufrette\StreamWrapper::getFilesystemMap();
            $map->set(self::$_stream_name, $filesystem);

            \Gaufrette\StreamWrapper::register();
        }
    }

    /**
     * Create a HTML image tag for the image specified by $path
     *
     * @param string $path image file path
     *
     * @return string html image tag
     */
    public function getImg($path)
    {
        $file = FILE_ROOT . $path;
        if (!file_exists($file)) {
            return null;
        }

        list($width, $height, $type, $attr) = getimagesize($file);

        $tag = "<img src='" . WEBROOT . "/file/path/" . $path . "' border='0' " . $attr . " />";
        return $tag;
    }

    /**
     * Create a HTML image tag for a thumbnail of the image specified by $path
     *
     * @param string $path image file path
     *
     * @return string thumbnail image url
     */
    public function getThumbImg($path)
    {
        $file = FILE_ROOT . $path;
        if (!file_exists($file)) {
            return $path . " does not exist.";
        }

        list($width, $height, $type, $attr) = getimagesize($file);

        $tag = "<img src='" . WEBROOT . "/file/thumb/" . $path . "' height='" . self::$_thumb_height . "' width='" . self::$_thumb_width . "' />";
        return $tag;
    }

    /**
     * Check if an attachment is an image
     *
     * @param string $path image file path
     *
     * @return bool
     */
    public function isImage($path)
    {
        if (file_exists(str_replace("'", "\\'", FILE_ROOT . "/" . $path))) {
            $path = str_replace("'", "\\'", FILE_ROOT . "/" . $path);
            $attr = null;
            if (is_file($path)) {
                list($width, $height, $type, $attr) = getimagesize($path);
            }
            return $attr !== null;
        } else {
            return false;
        }
    }

    /**
     * Get a url to the file specified by $path
     *
     * @return string URL to download file
     */
    public function getDownloadUrl($path)
    {
        return WEBROOT . "/file/path/" . $path;
    }

    /**
     * Lookup the attachments for a given object
     *
     * @param mixed $objectOrTable
     * @param mixed $id
     * @param array $type_code_blacklist a list of type codes to exclude
     *
     * @return array of the paths of the attached files
     */
    public function getAttachmentsFileList($objectOrTable, $id = null, $type_code_blacklist = [])
    {
        $attachments = $this->getAttachments($objectOrTable, $id);
        if (empty($attachments)) {
            return [];
        }

        foreach ($attachments as $key => $attachment) {
            if ($attachment->isRestricted()) {
                unset($attachments[$key]);
            }
        }

        $pluck = [];

        if (!empty($type_code_blacklist)) {
            $attachments = array_filter($attachments, function ($attachment) use ($type_code_blacklist) {
                if (in_array($attachment->type_code, $type_code_blacklist)) {
                    return false;
                }
                return true;
            });
        }

        foreach ($attachments ?? [] as $attachment) {
            $file_path = $attachment->getFilePath();

            if ($file_path[strlen($file_path) - 1] !== '/') {
                $file_path .= '/';
            }

            if ($attachment->adapter === "s3") {
                $pluck[] = Config::get("file.adapters.s3.options.directory") . "/" . $attachment->fullpath;
            } else {
                $pluck[] = realpath($file_path . $attachment->filename);
            }
        }

        return $pluck;
    }

    /**
     * Lookup the attachments for a given object
     *
     * @param mixed $object_or_table
     * @param mixed $id
     * @param int|null $page
     * @param int|null $page_size
     *
     * @return array[Attachment]
     */
    public function getAttachments($object_or_table, $id = null, ?int $page = null, ?int $page_size = null): array
    {
        $table = "";

        if (is_scalar($object_or_table)) {
            $table = $object_or_table;
        } elseif (is_a($object_or_table, "DbObject")) {
            $table = $object_or_table->getDbTableName();
            $id = $object_or_table->id;
        }

        return $this->getObjectsFromRows("Attachment", $this->_db->get("attachment")
            ->where("parent_table", $table)
            ->and("parent_id", $id)
            ->and("is_deleted", 0)
            ->paginate($page, $page_size)
            ->fetchAll());
    }

    public function getAttachmentsForAdapter($adapter)
    {
        if (Config::get('file.adapters.' . $adapter) !== null) {
            return $this->getObjects('Attachment', ['adapter' => $adapter, 'is_deleted' => 0]);
        }
    }

    /**
     * Counts attachments for a given object/table and id
     *
     * @param mixed $object_or_table
     * @param int $id
     *
     * @return int
     */
    public function countAttachments($object_or_table, $id = null)
    {
        if (is_scalar($object_or_table)) {
            $table = $object_or_table;
        } elseif (is_a($object_or_table, "DbObject")) {
            $table = $object_or_table->getDbTableName();
            $id = $object_or_table->id;
        }

        if ($table && $id) {
            return $this->_db->get("attachment")->where("parent_table", $table)->and("parent_id", $id)->and("is_deleted", 0)->count();
        }

        return 0;
    }

    /**
     * Counts attachments for a given object/table and id
     *
     * @param mixed $objectOrTable
     * @param int (option) $id
     *
     * @return int
     */
    public function countAttachmentsForUser($object, $user)
    {
        if (empty($object) || empty($user) || !is_a($object, "DbObject")) {
            return 0;
        }

        return $this->_db->get("attachment")->where('creator_id', $user->id)
            ->and("parent_table", $object->getDbTableName())->and("parent_id", $object->id)->and("is_deleted", 0)->count();
    }

    /**
     * Load a single attachment
     *
     * @param mixed $id attachment ID
     *
     * @return Attachment|null
     */
    public function getAttachment($id)
    {
        return $this->getObject("Attachment", $id);
    }


    /**
     * Get core cmfive s3 client, radically below abstraction of Gaufrette Filesystem
     *
     * @return S3Client
     */
    public function getS3ClientBelowFilesystem()
    {
        $args = [
            "region" =>  Config::get("file.adapters.s3.region", "ap-southeast-2"),
            "version" => Config::get("file.adapters.s3.version", "2006-03-01"),
        ];

        if (Config::get("system.environment", ENVIRONMENT_PRODUCTION) === ENVIRONMENT_DEVELOPMENT) {
            $args["credentials"] = Config::get("file.adapters.s3.credentials");
        }

        return new S3Client($args);
    }


    /**
     * Sends header and content of file to browser without intermediaries:
     * defaults to via filestream & exit(0)=Terminates execution!
     * otherwise, for s3, redirects to presigned url
     * In both cases, allows for largest possible file size by bypassing
     * PHP memory handling of data
     * @param Attachment $att The Attachment
     * @param string $saveAs Override Filename for browser as string
     * @return void
     */
    public function writeOutAttachment(Attachment $att, ?string $saveAs = null): void
    {
        // switch ($att->adapter) {
        //     case "s3":
        //         $client = $this->getS3Client();
        //         $cmd = $client->getCommand('GetObject', [
        //             'Bucket' => Config::get('file.adapters.s3.bucket'),
        //             'Key' => $att->getFilePath() . DS . $att->filename
        //         ]);

        //         $request = $client->createPresignedRequest($cmd, '+300 minutes');

        //         // Get the actual presigned-url
        //         $this->w->redirect((string)$request->getUri());
        //         break;

        //     default:
                $this->w->setLayout(null);
                // per : https://www.php.net/manual/en/function.readfile.php
                // readfile() will not present any memory issues on its own.
                // If you encounter an out of memory error ensure that output buffering is off
                if (ob_get_level()) {
                    ob_end_clean();
                }
                $this->w->header('Content-Description: File Transfer');
                $this->w->header('Content-Type: ' . (empty($att->mimetype) ? "application/octet-stream" : $att->mimetype));
                $this->w->header('Content-Disposition: attachment; filename="' . ($saveAs ?? $att->filename) . '"');
                $this->w->header('Expires: 0');
                $this->w->header('Cache-Control: must-revalidate');
                $this->w->header('Pragma: public');

                $filesystem = $att->getFileSystem();
                try {
                    $this->w->header('Content-Length: ' . $filesystem->fileSize($att->filename));
                } catch (Exception $e) {
                    LogService::getInstance($this->w)->error('Attachment write out error: ' . $e->getMessage());
                }
                echo $filesystem->read($att->filename);
                exit(0);
        // }
    }

    /**
     * Return the path adjusted to the currently active adapter.
     *
     * @param string file path
     *
     * @return string resulting file path
     */
    public function getFilePath($path)
    {
        $active_adapter = $this->getActiveAdapter();

        switch ($active_adapter) {
            case "s3":
                if (strpos($path, "uploads/") === false) {
                    return "uploads/" . $path;
                }
                return $path;
            default:
                if (strpos($path, FILE_ROOT . "attachments/") !== false) {
                    return $path;
                }
                if (strpos($path, "attachments/") !== false) {
                    return FILE_ROOT . $path;
                }

                return FILE_ROOT . "attachments/" . $path;
        }
    }

    /**
     * Move an uploaded file from the temp location to /files/attachments/<attachTable>/<year>/<month>/<day>/<attachId>/<filename> and create an Attachment record.
     *
     * @param string $request_key
     * @param DbObject $parentObject
     * @param string $title
     * @param string $description
     * @param string $type_code
     * @param boolean $is_public
     *
     * @return mixed the id of the attachment object or null
     */
    public function uploadAttachment($request_key, $parentObject, $title = null, $description = null, $type_code = null, $is_public = false, $filter_types = [])
    {
        if (empty($_POST[$request_key]) && (empty($_FILES[$request_key]) || $_FILES[$request_key]['size'] <= 0)) {
            return false;
        }

        if (!is_a($parentObject, "DbObject")) {
            $this->w->error("Parent not found.");
        }

        $replace_empty = ["..", "'", '"', ",", "\\", "/"];
        $replace_underscore = [" ", "&", "+", "$", "?", "|", "%", "@", "#", "(", ")", "{", "}", "[", "]", ",", ";", ":"];

        //Check for posted content
        if (!empty($_POST[$request_key]) && empty($_FILES[$request_key])) {
            $filename = str_replace($replace_underscore, "_", str_replace($replace_empty, "", $_POST[$request_key]));
        } else {
            $filename = str_replace($replace_underscore, "_", str_replace($replace_empty, "", basename($_FILES[$request_key]['name'])));
        }

        $att = new Attachment($this->w);
        $att->filename = $filename;
        $att->fullpath = null;
        $att->parent_table = $parentObject->getDbTableName();
        $att->parent_id = $parentObject->id;
        $att->title = (!empty($title) ? $title : $filename);
        $att->description = $description;
        $att->type_code = $type_code;
        $att->is_public = $is_public;
        $att->adapter = $this->getActiveAdapter();

        $filesystemPath = "attachments/" . $parentObject->getDbTableName() . '/' . date('Y/m/d') . '/' . $parentObject->id . '/';
        $filesystem = $this->getFilesystem($this->getFilePath($filesystemPath));

        if (empty($filesystem)) {
            LogService::getInstance($this->w)->setLogger("FILE_SERVICE")->error("Cannot save file, no filesystem returned");
            return null;
        }
        
        if (!empty($filter_types)) {
            if (!$this->fileIsInAllowedMimetypes($_FILES[$request_key]['tmp_name'], $filter_types, true)) {
                LogService::getInstance($this->w)->error('File upload is of a restricted type');
                return null;
            }
        }

        $att->fullpath = str_replace(FILE_ROOT, "", $filesystemPath . $filename);

        //Check for posted content
        if (!empty($_POST[$request_key])) {
            preg_match('%data:(.*);base%', substr($_POST[$request_key], 0, 25), $mime);
            $data = substr($_POST[$request_key], strpos($_POST[$request_key], ",") + 1);
            $mime_type = $mime[1];
            $content = base64_decode($data);

            $filesystem->write('/uploads/' . $att->fullpath, $content);
        } else {
            $local_filesystem = $this->getSpecificFilesystemWithCustomAdapter('local', null, '/tmp');

            try {
                $filesystem->writeStream($att->filename, $local_filesystem->readStream(basename($_FILES[$request_key]['tmp_name'])));
            } catch (Exception $exception) {
                // handle the error
                throw $exception;
            }

            // $file->setContent($content);
            switch ($att->adapter) {
                case "local":
                    $mime_type = $this->w->getMimetype(FILE_ROOT . $att->fullpath);
                    break;
                default:
                    $mime_type = $local_filesystem->mimeType(basename($_FILES[$request_key]['tmp_name']));
            }
        }

        $att->mimetype = $mime_type;
        $att->insert();

        return $att->id;
    }

    /**
     * Uploads multiple attachments at once (Using the Html::multiFileUpload function
     *
     *  Stores in /uploads/attachments/<ObjectTableName>/<year>/<month>/<day>/<attachId>/<filename>
     *
     * @param string $request_Key
     * @param DbObject $parentObject
     * @param array $titles
     * @param array $descriptions
     * @param array $type_codes
     *
     * @return bool if upload was successful
     */
    public function uploadMultiAttachment($request_key, $parentObject, $titles = null, $descriptions = null, $type_codes = null, $filter_types = [])
    {
        if (!is_a($parentObject, "DbObject")) {
            $this->w->error("Parent object not found.");
            return false;
        }
        
        $rpl_nil = ["..", "'", '"', ",", "\\", "/"];
        $rpl_ws = [" ", "&", "+", "$", "?", "|", "%", "@", "#", "(", ")", "{", "}", "[", "]", ",", ";", ":"];

        if (!empty($_FILES[$request_key]['name']) && is_array($_FILES[$request_key]['name'])) {
            $file_index = 0;
            foreach ($_FILES[$request_key]['name'] as $FILE_filename) {
                // Files can be empty
                if (!empty($FILE_filename)) {
                    $filename = str_replace($rpl_ws, "_", str_replace($rpl_nil, "", basename($FILE_filename)));
                    $att = new Attachment($this->w);
                    $att->filename = $filename;
                    $att->fullpath = null;
                    $att->parent_table = $parentObject->getDbTableName();
                    $att->parent_id = $parentObject->id;
                    $att->title = (!empty($titles[$file_index]) ? $titles[$file_index] : '');
                    $att->description = (!empty($descriptions[$file_index]) ? $descriptions[$file_index] : '');
                    $att->type_code = (!empty($type_codes) ? $type_codes[$file_index] : '');
                    
                    $filesystemPath = "attachments/" . $parentObject->getDbTableName() . '/' . date('Y/m/d') . '/' . $parentObject->id . '/';
                    $filesystem = $this->getFilesystem($this->getFilePath($filesystemPath));
                    if (empty($filesystem)) {
                        LogService::getInstance($this->w)->setLogger("FILE_SERVICE")->error("Cannot save file, no filesystem returned");
                        return null;
                    }
                    
                    $file = new FilePolyfill($filename, $filesystem);
                    
                    $att->adapter = $this->getActiveAdapter();
                    $att->fullpath = str_replace(FILE_ROOT, "", $filesystemPath . $filename);
                    
                    $content = file_get_contents($_FILES[$request_key]['tmp_name'][$file_index]);

                    if (!empty($filter_types) && !$this->fileIsInAllowedMimetypes($_FILES[$request_key]['tmp_name'][$file_index], $filter_types, true)) {
                        LogService::getInstance($this->w)->error('File upload is of a restricted type');
                    } else {
                        $file->setContent($content);
                        switch ($att->adapter) {
                            case "local":
                                $att->mimetype = $this->w->getMimetype(FILE_ROOT . $att->fullpath);
                                break;
                            default:
                                $att->mimetype = $this->w->getMimetypeFromString($content);
                        }
                        $att->insert();
                    }
                }
                $file_index++;
            }
        }

        return true;
    }

    /**
     * Removes a list of attachments by ID from an object
     *
     * @param DbObject $object
     * @param array $attachment_ids
     * @return void
     */
    public function removeAttachmentsByID(DbObject $object, array $attachment_ids)
    {
        foreach ($attachment_ids as $attachment_id) {
            $attachment = $this->getAttachment($attachment_id);
            if ($attachment->parent_table = $object->getDbTableName() && $attachment->parent_id == $object->id && $attachment->canDelete(AuthService::getInstance($this->w)->user())) {
                $attachment->delete();
            }
        }
    }

    /**
     * Save an attachment and create a file based on content passed as a parameter
     *
     * @param DbObject $object object to save content to
     * @param string $content file content
     * @param mixed $name
     * @param mixed $type_code
     * @param mixed $content_type
     *
     * @return int Attachment ID
     */
    public function saveFileContent($object, $content, $name = null, $type_code = null, $content_type = null, $description = null, $filter_types = [])
    {
        $filename = (!empty($name) ? $name : (str_replace(".", "", microtime()) . getFileExtension($content_type)));

        $att = new Attachment($this->w);
        $att->filename = $filename;
        $att->fullpath = null;
        $att->parent_table = $object->getDbTableName();
        $att->parent_id = $object->id;
        $att->title = (!empty($name) ? $name : $filename);
        $att->description = $description;
        $att->type_code = $type_code;
        $att->mimetype = "text/plain";
        $att->insert();

        $filesystemPath = "attachments/" . $object->getDbTableName() . '/' . date('Y/m/d') . '/' . $object->id . '/';
        $filesystem = $this->getFilesystem($this->getFilePath($filesystemPath));
        if (empty($filesystem)) {
            LogService::getInstance($this->w)->setLogger("FILE_SERVICE")->error("Cannot save file, no filesystem returned");
            return null;
        }

        $file = new FilePolyfill($filename, $filesystem);

        $att->adapter = $this->getActiveAdapter();
        $att->fullpath = str_replace(FILE_ROOT, "", $filesystemPath . $filename);

        if (!empty($filter_types)) {
            $file_type_data = '';
            switch ($this->getActiveAdapter()) {
                case "local":
                    $file_type_data = FILE_ROOT . $att->fullpath;
                    break;
                default:
                    $file_type_data = $content;
            }

            if (!$this->fileIsInAllowedMimetypes($file_type_data, $filter_types)) {
                LogService::getInstance($this->w)->error('File upload is of a restricted type');
                return null;
            }
        }

        //Check for posted content
        $file->setContent($content);
        switch ($att->adapter) {
            case "local":
                $att->mimetype = $this->w->getMimetype(FILE_ROOT . $att->fullpath);
                break;
            default:
                $att->mimetype = $this->w->getMimetypeFromString($content);
        }

        $att->update();

        return $att->id;
    }

    /**
     * Get the attachment types for a given object type
     *
     * @param DbObject $object
     *
     * @return array<AttachmentType>
     */
    public function getAttachmentTypesForObject($object)
    {
        return $this->getObjects("AttachmentType", ["table_name" => $object->getDbTableName(), "is_active" => '1']);
    }

    /**
     * Render a template showing an attachment
     *
     * @param DbObject $object
     * @param string $backUrl
     *
     * @return string
     */
    public function getImageAttachmentTemplateForObject($object, $backUrl)
    {
        $attachments = $this->getAttachments($object);
        $template = "";
        foreach ($attachments as $att) {
            if ($att->isImage()) {
                $template .= '
                <div class="attachment">
                <div class="thumb"><a
                    href="' . WEBROOT . '/file/atthumb/' . $att->id . '/800/600/a.jpg"
                    rel="gallery"><img
                    src="' . WEBROOT . '/file/atthumb/' . $att->id . '/250/250" border="0" /></a><br/>' . $att->description . '
                </div>

                <div class="actions">' . Html::a(WEBROOT . "/file/atdel/" . $att->id . "/" . $backUrl . "+" . $object->id, "Delete", null, null, "Do you want to delete this attachment?")
                    . ' ' . Html::a(WEBROOT . "/file/atfile/" . $att->id . "/" . $att->filename, "Download") . '
                </div>
                </div>';
            }
        }
        return $template;
    }

    /**
     * Returns the cache runtime path.
     *
     * @return string
     */
    public static function getCacheRuntimePath(): string
    {
        if (self::$cache_runtime_path === null) {
            self::$cache_runtime_path = uniqid();
        }

        return self::$cache_runtime_path;
    }

    public function fileIsInAllowedMimetypes($file, $allowed_extensions = [], $force_local = false)
    {
        $mimetype = '';
        if ($force_local) {
            $mimetype = $this->w->getMimetype($file);
        } else {
            switch ($this->getActiveAdapter()) {
                case "local":
                    $mimetype = $this->w->getMimetype($file);
                    break;
                default:
                    $mimetype = $this->w->getMimetypeFromString($file);
            }
        }

        $current_list = $this->getMimetypeList();
        foreach ($allowed_extensions as $ext) {
            if (array_key_exists($ext, $current_list) && ((is_array($current_list[$ext]) && in_array($mimetype, $current_list[$ext])) || $mimetype == $current_list[$ext])) {
                return true;
            }
        }

        return false;
    }

    public function getMimetypeList(): array
    {
       return [
            '.aac'  => 'audio/aac',
            '.abw'  => 'application/x-abiword',
            '.arc'  => 'application/x-freearc',
            '.avi'  => 'video/x-msvideo',
            '.azw'  => 'application/vnd.amazon.ebook',
            '.bin'  => 'application/octet-stream',
            '.bmp'  => 'image/bmp',
            '.bz'   => 'application/x-bzip',
            '.bz2'  => 'application/x-bzip2',
            '.cda'  => 'application/x-cdf',
            '.csh'  => 'application/x-csh',
            '.css'  => 'text/css',
            '.csv'  => 'text/csv',
            '.doc'  => 'application/msword',
            '.docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            '.eot'  => 'application/vnd.ms-fontobject',
            '.epub' => 'application/epub+zip',
            '.gz'   => 'application/gzip',
            '.gif'  => 'image/gif',
            '.htm'  => 'text/html',
            '.html' => 'text/html',
            '.ico'  => 'image/vnd.microsoft.icon',
            '.ics'  => 'text/calendar',
            '.jar'  => 'application/java-archive',
            '.jpg'  => 'image/jpeg',
            '.jpeg' => 'image/jpeg',
            '.js'   => 'text/javascript',
            '.json' => 'application/json',
            '.jsonld'   => 'application/ld+json',
            '.mid'  => 'audio/midi',
            '.midi' => 'audio/x-midi',
            '.mjs'  => 'text/javascript',
            '.mp3'  => 'audio/mpeg',
            '.mp4'  => 'video/mp4',
            '.mpeg' => 'video/mpeg',
            '.mpkg' => 'application/vnd.apple.installer+xml',
            '.odp'  => 'application/vnd.oasis.opendocument.presentation',
            '.ods'  => 'application/vnd.oasis.opendocument.spreadsheet',
            '.odt'  => 'application/vnd.oasis.opendocument.text',
            '.oga'  => 'audio/ogg',
            '.ogv'  => 'video/ogg',
            '.ogx'  => 'application/ogg',
            '.opus' => 'audio/opus',
            '.otf'  => 'font/otf',
            '.png'  => 'image/png',
            '.pdf'  => 'application/pdf',
            '.php'  => 'application/x-httpd-php',
            '.ppt'  => 'application/vnd.ms-powerpoint',
            '.pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            '.rar'  => 'application/vnd.rar',
            '.rtf'  => 'application/rtf',
            '.sh'   => 'application/x-sh',
            '.svg'  => 'image/svg+xml',
            '.swf'  => 'application/x-shockwave-flash',
            '.tar'  => 'application/x-tar',
            '.tif'  => 'image/tiff',
            '.tiff' => 'image/tiff',
            '.ts'   => 'video/mp2t',
            '.ttf'  => 'font/ttf',
            '.txt'  => 'text/plain',
            '.vsd'  => 'application/vnd.visio',
            '.wav'  => 'audio/wav',
            '.weba' => 'audio/webm',
            '.webm' => 'video/webm',
            '.webp' => 'image/webp',
            '.woff' => 'font/woff',
            '.woff2'    => 'font/woff2',
            '.xhtml'    => 'application/xhtml+xml',
            '.xls'  => 'application/vnd.ms-excel',
            '.xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            '.xml'  => 'application/xml',
            '.xul'  => 'application/vnd.mozilla.xul+xml',
            '.zip'  => 'application/zip',
            '.3gp' => ['video/3gpp', 'audio/3gpp'],
            '.3g2' => ['video/3gpp2', 'audio/3gpp2'],
            '.7z'   => 'application/x-7z-compressed',
        ];
    }
}
