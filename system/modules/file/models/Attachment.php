<?php

define('CACHE_PATH', 'cache');

use Gaufrette\Filesystem;
use Gaufrette\File as File;
use Gaufrette\Adapter\Local as LocalAdapter;
use Gaufrette\Adapter\InMemory as InMemoryAdapter;
use Gaufrette\Adapter\AwsS3 as AwsS3;
use Aws\S3\S3Client as S3Client;

class Attachment extends DbObject {

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

	/**
	 * DbObject::insert() override to set the mimetype, path and to call the
	 * attachment hook
	 * 
	 * @param <bool> $force_validation
	 */
	function insert($force_validation = false) {
		// Get mimetype
		if (empty($this->mimetype)) {
			$this->mimetype = $this->w->getMimetype(FILE_ROOT . "/" . $this->fullpath);
		}
		// $this->modifier_user_id = $this->w->Auth->user()->id; <-- why?
		$this->fullpath = str_replace(FILE_ROOT, "", $this->fullpath);

		// $this->filename = ($this->filename . getFileExtension($this->mimetype));

		$this->is_deleted = 0;
		parent::insert($force_validation);

		$this->w->callHook("attachment", "attachment_added_" . $this->parent_table, $this);
	}

	public function delete($force = false) {

		if ($this->hasCachedThumbnail()) {
			unlink($this->getThumbnailCachePath());
		}

		parent::delete($force);
	}

	function getParent() {
		return $this->getObject($this->parent_table, $this->parent_id);
	}

	/**
	 * will return true if this attachment
	 * is an image
	 * 
	 * @return <bool> is_image
	 */
	function isImage() {
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
	function getImg() {
		if ($this->isImage()) {
			return $this->File->getImg($this->fullpath);
		} else {
			
		}
	}

	/**
	 * if image, create image thumbnail
	 * if any other file send an icon for this mimetype
	 * 
	 * @return <String> url
	 */
	function getThumbnailUrl() {
		if ($this->isImage()) {
			return WEBROOT . "/file/atthumb/" . $this->id;
		}
		return null; // WEBROOT . "/img/document.jpg";
	}

	/**
	 * 	
	 * Returns html code for a thumbnail link to download this attachment
	 */
	function getThumb() {
		return Html::box($this->File->getDownloadUrl($this->fullpath), $this->File->getThumbImg($this->fullpath));
	}

	function getDownloadUrl() {
		return $this->File->getDownloadUrl($this->fullpath);
	}

	function getCodeTypeTitle() {
		$t = $this->w->Auth->getObject('AttachmentType', array('code' => $this->type_code, 'table_name' => $this->parent_table));

		if ($t) {
			return $t->title;
		} else {
			return null;
		}
	}

	public function getDocumentEmbedHtml($width = '1024', $height = '724') {
		if ($this->isDocument() && $this->adapter == 'local') {
			return Html::embedDocument($this->getViewUrl(), $width, $height);
		}
		return Html::a($this->getViewUrl(), $this->title);
	}

	/**
	 * Returns whether or not this attachment has a document mimetype
	 * 
	 * @return bool
	 */
	public function isDocument() {
		$document_mimetypes = ['application/pdf', 'application/msword', 'application/msword', 'application/rtf', 'application/vnd.ms-excel', 'application/vnd.ms-excel',
			'application/vnd.ms-powerpoint', 'application/vnd.ms-powerpoint', 'application/vnd.oasis.opendocument.text', 'application/vnd.oasis.opendocument.spreadsheet'];
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
	public function getFilePath() {
		$path = dirname($this->fullpath);

		switch ($this->adapter) {
			case "s3":
				if (strpos($path, "uploads/") === FALSE) {
					return "uploads/" . $path;
				}
				return $path;
			default:
				if (strpos($path, FILE_ROOT . "attachments/") !== FALSE) {
					return $path;
				}
				if (strpos($path, "attachments/") !== FALSE) {
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
	public function getFilesystem() {
		return $this->File->getSpecificFilesystem($this->adapter, $this->getFilePath());
	}

	/**
	 * Returns attachment mimetype
	 * @return <String> mimetype
	 */
	public function getMimetype() {
		return $this->mimetype;
	}

	/**
	 * Retuns Gaufrette File instance (of the attached file)
	 * @return \Gaufrette\File
	 */
	public function getFile() {
		return new \Gaufrette\File($this->filename, $this->getFilesystem());
	}

	/**
	 * Returns attached file content
	 * @return <string> content
	 */
	public function getContent() {
		$file = $this->getFile();
		return $file->exists() ? $file->getContent() : "";
	}

	/**
	 * Sends header and content of file to browser
	 */
	public function displayContent() {
		$this->w->header("Content-Type: " . $this->getMimetype());
		$this->w->out($this->getContent());
	}

	/**
	 * Moves the content from one adapter to another
	 */
	public function moveToAdapter($adapter = "local", $delete_after_move = false) {
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
	public function getViewUrl() {
		return "/file/atfile/" . $this->id;
	}

	/**
	 * Returns thumbnail cache path
	 */
	public function getThumbnailCachePath() {
		return ROOT_PATH . '/' . CACHE_PATH . '/' . $this->fullpath;
	}

	/**
	 * Returns if there is a cache thumbnail image
	 */
	public function hasCachedThumbnail() {
		if ($this->isImage()) {
			return is_file($this->getThumbnailCachePath());
		}
		return false;
	}

	/**
	 * replaces an attachments file with a new one
	 */
	public function updateAttachment($requestkey) {
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

	function createCachedThumbnail($width = null, $height = null) {
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

	function getSelectOptionTitle() {
		return $this->filename;
	}

	function getSelectOptionValue() {
		return $this->filename;
	}

}
