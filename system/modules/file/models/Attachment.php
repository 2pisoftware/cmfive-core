<?php

define('CACHE_PATH', 'cache');
define('IMAGE_PATH', 'image');

use Gaufrette\Filesystem;
use Gaufrette\File as File;
use Gaufrette\Adapter\Local as LocalAdapter;
use Gaufrette\Adapter\InMemory as InMemoryAdapter;
use Gaufrette\Adapter\AwsS3 as AwsS3;
use Aws\S3\S3Client as S3Client;

class Attachment extends DbObject
{

    public $parent_table;
    public $parent_id;
    public $dt_created; // datetime
    public $dt_modified; // datetime
    public $modifier_user_id; // bigint
    public $filename; // publicchar(255)
    public $mimetype; // publicchar(255)
    public $title; // publicchar(255)
    public $description; // text
    public $fullpath; // publicchar(255)
    public $is_deleted; // tinyint 0/1
    public $type_code; // this is a type of attachment, eg. Receipt of Deposit, PO Variation, Sitephoto, etc.
    public $adapter;
    public $is_public;
    public $_restrictable;
    public $dt_viewing_window; // dt of access to list attachments. checked against config file.docx_viewing_window_duration to bypass authentication.

    /**
     * Used by the task_attachment_attachment_added_task hook to skip the Attachement added notification if true
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
        $this->fullpath = str_replace(FILE_ROOT, "", $this->fullpath);
        // Get mimetype
        if (empty($this->mimetype)) {
            $this->mimetype = $this->w->getMimetype($this->fullpath);
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
            return $this->File->getImg($this->fullpath);
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
        return null; // WEBROOT . "/img/document.jpg";
    }

    /**
     *
     * Returns html code for a thumbnail link to download this attachment
     */
    public function getThumb()
    {
        return Html::box($this->File->getDownloadUrl($this->fullpath), $this->File->getThumbImg($this->fullpath));
    }

    public function getDownloadUrl()
    {
        return $this->File->getDownloadUrl($this->fullpath);
    }

    public function getCodeTypeTitle()
    {
        $t = $this->w->Auth->getObject('AttachmentType', array('code' => $this->type_code, 'table_name' => $this->parent_table));

        if ($t) {
            return $t->title;
        } else {
            return null;
        }
    }

    public function getDocumentEmbedHtml($width = '1024', $height = '724')
    {
        $view_url = $this->getViewUrl();
        if ($this->isDocument() && $this->adapter == 'local') {
            if (stripos($this->filename, '.docx') || stripos($this->filename, '.doc')) {
                $view_url = substr($view_url, 0, 1) == '/' ? substr($view_url, 1) : $view_url;
                return Html::embedDocument($this->w->localUrl() . $view_url, $width, $height, 'page-width', true);
            } else {
                return Html::embedDocument($view_url, $width, $height);
            }
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
            'application/pdf', 'application/msword', 'application/msword', 'application/rtf', 'application/vnd.ms-excel', 'application/vnd.ms-excel',
            'application/vnd.ms-powerpoint', 'application/vnd.ms-powerpoint', 'application/vnd.oasis.opendocument.text', 'application/vnd.oasis.opendocument.spreadsheet', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
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
     * @return <String> filepath
     */
    public function getFilePath()
    {
        $path = dirname($this->fullpath);

        switch ($this->adapter) {
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
     * Returns Gaufrette Filsystem instance for fetching files
     *
     * @return \Gaufrette\Filesystem
     */
    public function getFilesystem()
    {
        return $this->File->getSpecificFilesystem($this->adapter, $this->getFilePath());
    }

    /**
     * Returns attachment mimetype
     * @return <String> mimetype
     */
    public function getMimetype()
    {
        return $this->mimetype;
    }

    /**
     * Retuns Gaufrette File instance (of the attached file)
     * @return \Gaufrette\File
     */
    public function getFile()
    {
        return new \Gaufrette\File($this->filename, $this->getFilesystem());
    }

    /**
     * Returns attached file content
     * @return <string> content
     */
    public function getContent()
    {
        $file = $this->getFile();
        return $file->exists() ? $file->getContent() : "";
    }

    /**
     * Sends header and content of file to browser
     */
    public function displayContent()
    {
        $this->w->header("Content-Type: " . $this->getMimetype());
        $this->w->out($this->getContent());
    }

    /**
     * Moves the content from one adapter to another
     */
    public function moveToAdapter($adapter = "local", $delete_after_move = false)
    {
        // Get content of file
        $content = $this->getContent();
        $current_file = $this->getFile();

        $this->adapter = $adapter;

        $filesystem = $this->getFilesystem();
        $file = new Gaufrette\File($this->filename, $filesystem);

        $file->setContent($content);

        if ($delete_after_move === true) {
            try {
                $current_file->delete();
            } catch (RuntimeException $ex) {
                $this->w->Log->setLogger("FILE")->error("Cannot delete file: " . $ex->getMessage());
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
        return ROOT_PATH . '/' . CACHE_PATH . '/' . $this->fullpath;
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

        return ROOT_PATH . "/" . CACHE_PATH . "/" . IMAGE_PATH . "/" . $path_info["dirname"] . "/" . $path_info["filename"] . ".jpg";
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

        $replace_empty = array("..", "'", '"', ",", "\\", "/");
        $replace_underscore = array(" ", "&", "+", "$", "?", "|", "%", "@", "#", "(", ")", "{", "}", "[", "]", ",", ";", ":");


        if (!empty($_POST[$requestkey]) && empty($_FILES[$requestkey])) {
            $filename = str_replace($replace_underscore, "_", str_replace($replace_empty, "", $_POST[$requestkey]));
        } else {
            $filename = str_replace($replace_underscore, "_", str_replace($replace_empty, "", basename($_FILES[$requestkey]['name'])));
        }

        $this->filename = $filename;

        $filesystemPath = "attachments/" . $this->parent_table . '/' . date('Y/m/d') . '/' . $this->parent_id . '/';
        $filesystem = $this->w->file->getFilesystem($this->w->file->getFilePath($filesystemPath));
        if (empty($filesystem)) {
            $this->w->Log->setLogger("FILE_SERVICE")->error("Cannot save file, no filesystem returned");
            return null;
        }

        $file = new File($filename, $filesystem);

        $this->adapter = $this->w->file->getActiveAdapter();
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
            $width = $this->w->request("w", FileService::$_thumb_width);
            $height = $this->w->request("h", FileService::$_thumb_height);
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
            default:
                $w->Log->setLogger("FILE")->error("Unable to convert image with mime type " . $image_info["mime"] . " to JPEG");
                return;
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
}
