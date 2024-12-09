<?php

/**
 * Use of variables created with define should be phased out in favour of public const property equivalent of an object.
 * These will deprecated and removed in a future version of Cmfive.
 * This will help with avoiding clashing variable names.
 */
define('CACHE_PATH', 'cache');
define('IMAGE_PATH', 'image');

use Gaufrette\File as File;

class Attachment extends DbObject
{
    public const CACHE_PATH = "cache";
    public const IMAGE_PATH = "image";
    public const TEMP_PATH = "temp";

    public const ADAPTER_LOCAL = "local";
    public const ADAPTER_S3 = "s3";

    public $parent_table;
    public $parent_id;
    public $dt_created;
    public $dt_modified;
    public $modifier_user_id;
    public $filename;
    public $mimetype;
    public $title;
    public $description;
    public $fullpath;
    public $is_deleted;
    public $type_code; // this is a type of attachment, eg. Receipt of Deposit, PO Variation, Sitephoto, etc.
    public $adapter;
    public $is_public;
    public $_restrictable;
    public $dt_viewing_window; // dt of access to list attachments. checked against config file.docx_viewing_window_duration to bypass authentication.
    public $skip_path_prefix;

    /**
     * EXIF image data that is captured from image Attachments. This property is lazily loaded
     * and therefore may be null. Use Attachment::getImageExifData() to load and fetch the data.
     *
     * @var string
     */
    public $exif_data;

    /**
     * XMP image data that is catpured from image Attachments. This property is lazily loaded
     * and therefore may be null. Use Attachment::getImageXmpData() to load and fetch the data.
     *
     * @var string
     */
    public $xmp_data;

    /**
     * Used by the task_attachment_attachment_added_task hook to skip the Attachment added notification if true
     * @var boolean
     */
    public $_skip_added_notification;

    /**
     * DbObject::insert() override to set the mimetype, path and to call the
     * attachment hook
     *
     * @param <bool> $force_validation
     */

    public function insert($force_validation = false)
    {
        $this->fullpath = str_replace(FILE_ROOT, "", $this->fullpath ?? "");
        // Get mimetype
        if (empty($this->mimetype)) {
            switch ($this->adapter) {
                case "local":
                    $this->mimetype = $this->w->getMimetype($this->fullpath);
                    break;
                default:
                    $this->mimetype = $this->w->getMimetypeFromString($this->getContent());
            }
        }

        $this->is_deleted = 0;
        parent::insert($force_validation);

        $this->w->callHook("attachment", "attachment_added_" . $this->parent_table, $this);
    }

    public function delete($force = false)
    {
        if ($this->hasCachedThumbnail()) {
            unlink($this->getThumbnailCachePath());
        }

        parent::delete($force);
    }

    public function getParent()
    {
        if (class_exists($this->parent_table)) {
            return $this->getObject($this->parent_table, $this->parent_id);
        } else {
            $className = str_replace(' ', '', ucwords(str_replace('_', ' ', $this->parent_table)));
            return $this->getObject($className, $this->parent_id);
        }
    }

    /**
     * will return true if this attachment
     * is an image
     *
     * @return <bool> is_image
     */
    public function isImage()
    {
        // Attachment is an image when the mimetype starts with "image/"
        return strpos($this->mimetype, "image/") === 0;
    }

    /**
     * Returns a HTML <img> tag for this attachment
     * only if this attachment is an image,
     * else
     *
     * @return <String> image_string
     */
    public function getImg()
    {
        if ($this->isImage()) {
            return FileService::getInstance($this->w)->getImg($this->fullpath);
        }
    }

    /**
     * if image, create image thumbnail
     * if any other file send an icon for this mimetype
     *
     * @return <String> url
     */
    public function getThumbnailUrl()
    {
        if ($this->isImage()) {
            return WEBROOT . "/file/atthumb/" . $this->id;
        }
        return null;
    }

    /**
     *
     * Returns html code for a thumbnail link to download this attachment
     */
    public function getThumb()
    {
        return Html::box(FileService::getInstance($this->w)->getDownloadUrl($this->fullpath), FileService::getInstance($this->w)->getThumbImg($this->fullpath));
    }

    public function getDownloadUrl()
    {
        return FileService::getInstance($this->w)->getDownloadUrl($this->fullpath);
    }

    public function getCodeTypeTitle()
    {
        $t = AuthService::getInstance($this->w)->getObject('AttachmentType', ['code' => $this->type_code, 'table_name' => $this->parent_table]);

        if ($t) {
            return $t->title;
        } else {
            return null;
        }
    }

    public function getDocumentEmbedHtml($width = '1024', $height = '724')
    {
        $view_url = $this->getViewUrl();
        if ($this->isDocument()) {
            return Html::embedDocument($view_url, $width, $height);
        }

        return Html::a($view_url, $this->title);
    }

    /**
     * Returns whether or not this attachment has a document mimetype
     *
     * @return bool
     */
    public function isDocument()
    {
        $document_mimetypes = [
            'application/pdf',
            'application/msword',
            'application/msword',
            'application/rtf',
            'application/vnd.ms-excel',
            'application/vnd.ms-excel',
            'application/vnd.ms-powerpoint',
            'application/vnd.ms-powerpoint',
            'application/vnd.oasis.opendocument.text',
            'application/vnd.oasis.opendocument.spreadsheet',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/json',
            'text/plain'
        ];

        return in_array($this->mimetype, $document_mimetypes);
    }

    /**********
     * Gaufrette helper functions
     **********/

    /**
     * Returns an assembled file path based on the adapter
     * The local adapter for e.g. needs an absolute reference, this absolute
     * prefix isn't needed when using S3 buckets
     *
     * @return string
     */
    public function getFilePath(): string
    {
        if (file_exists(ROOT_PATH . "/" . Attachment::CACHE_PATH . "/" . Attachment::TEMP_PATH . "/" . FileService::getCacheRuntimePath() . "/" . $this->id . "/" . $this->dt_created . "/" . $this->filename)) {
            return ROOT_PATH . "/" . Attachment::CACHE_PATH . "/" . Attachment::TEMP_PATH . "/" . FileService::getCacheRuntimePath() . "/" . $this->id . "/" . $this->dt_created;
        }

        $path = dirname($this->fullpath ?? '');

        switch ($this->adapter) {
            case "s3":
                if ($this->skip_path_prefix) {
                    return $path;
                }

                if (strpos($path, "uploads/") === false) {
                    return "uploads/" . $path;
                }
                return $path;
            default:
                if ($this->skip_path_prefix) {
                    return $path;
                }

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
     * Returns Gaufrette Filsystem instance for fetching files
     *
     * @return League\Flysystem\Filesystem
     */
    public function getFilesystem(): League\Flysystem\Filesystem
    {
        return FileService::getInstance($this->w)->getSpecificFilesystem($this->adapter, $this->getFilePath());
    }

    /**
     * Returns attachment mimetype
     *
     * @return string mimetype
     */
    public function getMimetype(): string
    {
        return $this->mimetype;
    }

    /**
     * Get the bootstrap5 icon class associated with the mime-type of this attachment
     * Useful for fallback icons, for example for pdf and video files where there is no getThumbnailUrl()
     * @return string Bootstrap5 Icon class name
     */
    public function getBootstrap5IconClass(): string
    {
        if ($this->isDocument()) {
            return "bi-filetype-doc";
        }

        // return bi-filetype-doc here as a fallback
        return "bi-filetype-doc bi-filetype-" . end(explode(".", $this->filename));
    }

    /**
     * Returns Gaufrette File instance (of the attached file)
     *
     * @return FilePolyfill
     */
    public function getFile(): FilePolyfill
    {
        $cache_directory = ROOT_PATH . "/" . Attachment::CACHE_PATH . "/" . Attachment::TEMP_PATH . "/" . FileService::getCacheRuntimePath() . "/" . $this->id . "/" . $this->dt_created;
        $cached_file_path = $cache_directory . "/" . $this->filename;

        if (file_exists($cached_file_path)) {
            return new FilePolyfill($this->filename, FileService::getInstance($this->w)->getSpecificFilesystem("local", $cache_directory));
        }

        return new FilePolyfill($this->filename, $this->getFilesystem());
    }

    /**
     * Returns attached file content
     *
     * @return string content
     */
    public function getContent($cache_locally = false): string
    {
        $file = $this->getFile();
        if (empty($file) || !$file->exists()) {
            return "";
        }

        $cache_directory = ROOT_PATH . "/" . Attachment::CACHE_PATH . "/" . Attachment::TEMP_PATH . "/" . FileService::getCacheRuntimePath() . "/" . $this->id . "/" . $this->dt_created;
        $cache_file_path = $cache_directory . "/" . $this->filename;

        if ($this->adapter === "local" || !$cache_locally || file_exists($cache_file_path)) {
            return $file->getContent();
        }

        if (!file_exists($cache_directory)) {
            try {
                mkdir($cache_directory, 0771, true);
            } catch (Exception $e) {
                LogService::getInstance($this->w)->setLogger("FILE")->error("Failed to execute 'mkdir': " . $e->getMessage());
            }
        }

        try {
            file_put_contents($cache_file_path, $file->getContent());
            return $file->getContent();
        } catch (Exception $e) {
            LogService::getInstance($this->w)->setLogger("FILE")->error("Failed to execute 'file_put_contents': " . $e->getMessage());
        }

        return "";
    }

    /**
     * Sends header and content of file to browser
     */
    public function displayContent()
    {
        $this->w->header("Content-Type: " . $this->getMimetype());
        $this->w->header("Content-Disposition: inline; filename=\"" . $this->filename . "\"");
        $this->w->out($this->getContent());
    }

    /**
     * Sends header and content of file to browser without intermediaries, via exit(0)=Terminates execution!
     * @param string $saveAs Override Filename for browser 'save as'
     * @return void
     */
    public function writeOut(?string $saveAs = null): void
    {
        FileService::getInstance($this->w)->writeOutAttachment($this, $saveAs);
    }

    /**
     * Moves the content from one adapter to another
     */
    public function moveToAdapter($adapter = "local", $delete_after_move = false)
    {
        try {
            // Get content of file
            $content = $this->getContent();
            $current_file = $this->getFile();
        } catch (InvalidArgumentException $e) {
            LogService::getInstance($this->w)->setLogger("FILE")->error("Attachment's {id: $this->id} file does not exist at path: $this->fullpath");
            return;
        }

        $this->adapter = $adapter;

        $filesystem = $this->getFilesystem();
        $file = new FilePolyfill($this->filename, $filesystem);

        $file->setContent($content);

        if ($delete_after_move === true) {
            try {
                $current_file->delete();
            } catch (RuntimeException $ex) {
                LogService::getInstance($this->w)->setLogger("FILE")->error("Cannot delete file: " . $ex->getMessage());
            }
        }

        // Update the adapter location
        $this->update(false);
    }

    /**
     * Returns URL to view
     */
    public function getViewUrl()
    {
        return "/file/atfile/" . $this->id;
    }

    /**
     * Returns thumbnail cache path
     */
    public function getThumbnailCachePath()
    {
        return ROOT_PATH . '/' . Attachment::CACHE_PATH . '/' . $this->fullpath;
    }

    /**
     * Returns image cache path
     *
     * @return string
     */
    public function getImageCachePath()
    {
        $path_info = pathinfo($this->fullpath);

        if (!array_key_exists("dirname", $path_info)) {
            return null;
        }

        return ROOT_PATH . "/" . Attachment::CACHE_PATH . "/" . Attachment::IMAGE_PATH . "/" . $path_info["dirname"] . "/" . $path_info["filename"] . ".jpg";
    }

    /**
     * Returns if there is a cache thumbnail image
     */
    public function hasCachedThumbnail()
    {
        if ($this->isImage()) {
            return is_file($this->getThumbnailCachePath());
        }
        return false;
    }

    /**
     * Returns true if there is a cached image.
     *
     * @return boolean
     */
    public function hasCachedImage()
    {
        if ($this->isImage()) {
            return is_file($this->getImageCachePath());
        }

        return false;
    }

    /**
     * replaces an attachments file with a new one
     */
    public function updateAttachment($requestkey)
    {
        //Check for posted content
        if (empty($_FILES[$requestkey]) || $_FILES[$requestkey]['size'] <= 0) {
            return false;
        }

        $replace_empty = ["..", "'", '"', ",", "\\", "/"];
        $replace_underscore = [" ", "&", "+", "$", "?", "|", "%", "@", "#", "(", ")", "{", "}", "[", "]", ",", ";", ":"];

        if (!empty($_POST[$requestkey]) && empty($_FILES[$requestkey])) {
            $filename = str_replace($replace_underscore, "_", str_replace($replace_empty, "", $_POST[$requestkey]));
        } else {
            $filename = str_replace($replace_underscore, "_", str_replace($replace_empty, "", basename($_FILES[$requestkey]['name'])));
        }

        $this->filename = $filename;

        $filesystemPath = "attachments/" . $this->parent_table . '/' . date('Y/m/d') . '/' . $this->parent_id . '/';
        $filesystem = FileService::getInstance($this->w)->file->getFilesystem(FileService::getInstance($this->w)->file->getFilePath($filesystemPath));
        if (empty($filesystem)) {
            LogService::getInstance($this->w)->setLogger("FILE_SERVICE")->error("Cannot save file, no filesystem returned");
            return null;
        }

        $file = new File($filename, $filesystem);

        $this->adapter = FileService::getInstance($this->w)->file->getActiveAdapter();
        $this->fullpath = str_replace(FILE_ROOT, "", $filesystemPath . $filename);

        //Check for posted content
        if (!empty($_POST[$requestkey])) {
            preg_match('%data:(.*);base%', substr($_POST[$requestkey], 0, 25), $mime);
            $data = substr($_POST[$requestkey], strpos($_POST[$requestkey], ",") + 1);
            $mime_type = $mime[1];
            $content = base64_decode($data);
            $file->setContent($content, ['contentType' => $mime_type]);
        } else {
            $content = file_get_contents($_FILES[$requestkey]['tmp_name']);
            $file->setContent($content);
            switch ($this->adapter) {
                case "local":
                    $mime_type = $this->w->getMimetype(FILE_ROOT . $this->fullpath);
                    break;
                default:
                    $mime_type = $this->w->getMimetypeFromString($content);
            }
        }


        $this->mimetype = $mime_type;
        $this->update();

        if ($this->isImage()) {
            // Generate thumbnail and cache
            require_once 'phpthumb/ThumbLib.inc.php';
            $width = Request::int('w', FileService::$_thumb_width);
            $height = Request::int('h', FileService::$_thumb_height);
            $thumb = PhpThumbFactory::create($this->getContent(), [], true);
            $thumb->adaptiveResize($width, $height);

            // Create cached folder
            if (!is_dir(dirname($this->getThumbnailCachePath()))) {
                mkdir(dirname($this->getThumbnailCachePath()), 0755, true);
            }

            // Write thumbnail to cache
            file_put_contents($this->getThumbnailCachePath(), $thumb->getImageAsString());
        }
    }

    public function createCachedThumbnail($width = null, $height = null)
    {
        if ($this->isImage()) {
            // Generate thumbnail and cache
            require_once 'phpthumb/ThumbLib.inc.php';
            $width = (!empty($width) && is_int($width) ? $width : FileService::$_thumb_width);
            $height = (!empty($height) && is_int($height) ? $height : FileService::$_thumb_height);
            $thumb = PhpThumbFactory::create($this->getContent(), [], true);
            $thumb->adaptiveResize($width, $height);

            // Create cached folder
            if (!is_dir(dirname($this->getThumbnailCachePath()))) {
                mkdir(dirname($this->getThumbnailCachePath()), 0755, true);
            }

            // Write thumbnail to cache
            file_put_contents($this->getThumbnailCachePath(), $thumb->getImageAsString());
        }
    }

    public function createCachedImage()
    {
        require_once 'phpthumb/ThumbLib.inc.php';

        $full_file_path = $this->getFilePath() . "/" . $this->filename;

        if (!file_exists($full_file_path)) {
            return false;
        }

        $image_info = getimagesize($full_file_path);
        $width = $image_info[0];
        $height = $image_info[1];

        $original_image = null;
        $final_image = null;

        switch ($image_info["mime"]) {
            case image_type_to_mime_type(IMAGETYPE_JPEG):
                $original_image = imagecreatefromjpeg($full_file_path);
                break;
            case image_type_to_mime_type(IMAGETYPE_PNG):
                $original_image = imagecreatefrompng($full_file_path);
                break;
            case image_type_to_mime_type(IMAGETYPE_BMP):
                $original_image = imagecreatefrombmp($full_file_path);
                break;
            case image_type_to_mime_type(IMAGETYPE_GIF):
                $original_image = imagecreatefromgif($full_file_path);
                break;
            default:
                LogService::getInstance($this->w)->setLogger("FILE")->error("Unable to convert image with mime type " . $image_info["mime"] . " to JPEG");
                return false;
        }

        $max_width = Config::get("file.cached_image_max_width", 1920);

        if ($width > $max_width) {
            $reduction_ratio = $width / $max_width;
            $new_width = $max_width;
            $new_height = $height / $reduction_ratio;

            $final_image = imagecreatetruecolor($new_width, $new_height);
            imagecopyresampled($final_image, $original_image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
        }

        if (!is_dir(dirname($this->getImageCachePath()))) {
            mkdir(dirname($this->getImageCachePath()), 0755, true);
        }

        imagejpeg(empty($final_image) ? $original_image : $final_image, $this->getImageCachePath(), Config::get("file.cached_image_default_quality", -1));
        return true;
    }

    public function getSelectOptionTitle()
    {
        return $this->filename;
    }

    public function getSelectOptionValue()
    {
        return $this->filename;
    }

    public function checkViewingWindow()
    {
        if (stripos($this->filename, '.docx') || stripos($this->filename, '.doc') && !empty($this->dt_viewing_window)) {
            $viewing_duration = Config::get("file.docx_viewing_window_duration");
            $time = time();
            if ($this->dt_viewing_window >= $time - $viewing_duration && $time <= $this->dt_viewing_window + $viewing_duration) {
                return true;
            }
        }
        return false;
    }

    /**
     * Returns the image Attachment's EXIF data. If the exif_data property is not set the Attachment will
     * attempt to scrape it from the file. This method will only work on images.
     *
     * @return string|null
     */
    public function getImageExifData(): ?string
    {
        if (!$this->isImage()) {
            return null;
        }

        if (!empty($this->exif_data)) {
            return $this->exif_data;
        }

        $image_data = $this->getContent(true);

        // Because 'exif_read_data' throws a warning and not an exception when an unsupported image type is given we
        // have to set our own error handler temporarily to elevate the warning to an exception. The catch statement
        // will then pick it up and it can be handled.
        try {
            set_error_handler(function ($errno, $errmsg, $filename, $linenum, $vars) {
                if ($errno === E_WARNING) {
                    throw new Exception("Warning triggered, elevating to exception, errmsg: $errmsg, filename: $filename, linenum: $linenum");
                }
            });

            $this->exif_data = json_encode(exif_read_data("data://$this->mimetype;base64," . base64_encode($image_data)));
        } catch (Throwable $t) {
            LogService::getInstance($this->w)->setLogger("FILE")->error("Failed to read exif data: {$t->getMessage()}");
            return null;
        } finally {
            restore_error_handler();
        }

        if (!$this->update()) {
            LogService::getInstance($this->w)->setLogger("FILE")->error("Failed to update Attachment in the database");
            return null;
        }

        return $this->exif_data;
    }

    /**
     * Return the image Attachment's XMP data. IF the xmp_data property is not set the Attachment will
     * attempt to scrape it from the file. This method will only work on images.
     *
     * @return string|null
     */
    public function getImageXmpData(): ?string
    {
        if (!$this->isImage()) {
            return null;
        }

        if (!empty($this->xmp_data)) {
            return $this->xmp_data;
        }

        $image_data = $this->getContent(true);

        // Find where the XMP data starts and ends, then pull that substring out.
        $xmp_data_start = strpos($image_data, "<x:xmpmeta");
        $xmp_data_end = strpos($image_data, "</x:xmpmeta>");
        $xmp_length = $xmp_data_end - $xmp_data_start;
        $this->xmp_data = substr($image_data, $xmp_data_start, $xmp_length + 12);

        if (!$this->update()) {
            LogService::getInstance($this->w)->setLogger("FILE")->error("Failed to update Attachment in the database");
            return null;
        }

        return $this->xmp_data;
    }
}
