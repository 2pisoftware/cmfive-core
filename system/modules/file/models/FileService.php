<?php

use Gaufrette\Filesystem;
use Gaufrette\File as File;
use Gaufrette\Adapter\Local as LocalAdapter;
use Gaufrette\Adapter\InMemory as InMemoryAdapter;
use Gaufrette\Adapter\AwsS3 as AwsS3;
use Aws\S3\S3Client as S3Client;

/**
 * Service class with functions to help managing files and attachment records.
 * Encapsulates the Gaufrette library of file system adapters.
 */
class FileService extends DbService {

	public static $_thumb_height = 200;
	public static $_thumb_width = 200;
	public static $_stream_name = "attachment";

	/**
	 * Returns the max upload file size in bytes
	 *
	 * @return int $fileSize;
	 */
	public function getMaxFileUploadSize() {
		$ini_upload_limit = ini_get('upload_max_filesize');

		$units = 'B';
		if (intval($ini_upload_limit) !== $ini_upload_limit) {
			$units = substr($ini_upload_limit, -1);
			$ini_upload_limit = substr($ini_upload_limit, 0, strlen($ini_upload_limit) - 1);
		}

		$multiplier = 1;
		switch(strtoupper($units)) {
			case "G":
				$multiplier *= 1024;
			case "M":
				$multiplier *= 1024;
			case "K":
				$multiplier *= 1024;
				break;
		}

		return (int) $ini_upload_limit * $multiplier;
	}

	/**
	 * Return the path adjusted to the currently active adapter.
	 *
	 * @param string file path
	 * @return string resulting file path
	 */
	function getFilePath($path) {
		$active_adapter = $this->getActiveAdapter();
		
		switch($active_adapter) {
			case "local":
				if (strpos($path, FILE_ROOT . "attachments/") !== FALSE) {
					return $path;
				}
				if (strpos($path, "attachments/") !== FALSE) {
					return FILE_ROOT . $path;
				}

				return FILE_ROOT . "attachments/" . $path;
			default:
				if (strpos($path, "uploads/") === FALSE) {
					return "uploads/" . $path;
				}
				return $path;
		}
	}

	/**
	 * Create a new Gaufrette File object from a filename and path
	 * 
	 * @param \Gaufrette\Filesystem
	 * @param string filename
	 * @return \Gaufrette\File
	 */
	function getFileObject($filesystem, $filename) {
		$file = new File($filename, $filesystem);
		return $file;
	}

	/**
	 * Returns the first adapter maked as active that isn't "local".
	 * 
	 * The local filesystem is the default adapter if none are specified.
	 * 
	 * @return string adapter
	 */
	function getActiveAdapter() {
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
	 * @param Mixed $path base path to load the filesystem adapter at
	 * @param Mixed $content content to load into the filesystem (mainly used for the "memory" adapter
	 * @param Array $options options to give to the filesystem
	 * @return \Gaufrette\Filesystem
	 */
	function getFilesystem($path = null, $content = null, $options = []) {
		return $this->getSpecificFilesystem($this->getActiveAdapter(), $path, $content, $options);
	}
	
	/**
	 * Get a Gaufrette Filesystem for a given adapter and path
	 * 
	 * @param string $adapter adapter to load
	 * @param string $path base path to load the filesystem adapter at
	 * @param Mixed $content content to load into the filesystem (mainly used for the "memory" adapter
	 * @param Array $options options to give to the filesystem
	 * @return FileSystem
	 */	
	function getSpecificFilesystem($adapter = "local", $path = null, $content = null, $options = []) {
		$adapter_obj = null;
		switch ($adapter) {
			case "local":
				$adapter_obj = new LocalAdapter($path, true);
				break;
			case "memory":
				$adapter_obj = new InMemoryAdapter(array(basename($path) => $content));
				break;
			case "s3":
				$client = new Aws\S3\S3Client(Config::get('file.adapters.s3'));
				$config_options = Config::get('file.adapters.s3.options');
				$config_options = array_replace(is_array($config_options) ? $config_options : [], ["directory" => $path], $options);
				// $client = S3Client::factory(["key" => Config::get('file.adapters.s3.key'), "secret" => Config::get('file.adapters.s3.secret')]);
				$adapter_obj = new AwsS3($client, Config::get('file.adapters.s3.bucket'), is_array($config_options) ? $config_options : []);
				break;
			case "dropbox":
				if (!function_exists("oauth")) {
					$this->w->Log->setLogger("FILE_SERVICE")->error("The Dropbox API requires the oAuth extension to be installed.");
					return null;
				}
				$app_id = Config::get('file.adapters.dropbox.app_id');
				if (!empty($app_id)) {
					$adapter_obj = new \Gaufrette\Adapter\Dropbox(new Dropbox_API(new Dropbox_OAuth_PHP($app_id, '')));
				} else {
					$this->w->Log->setLogger("FILE_SERVICE")->error("Dropbox adapter requested but no app ID has been provided in the config.");
				}
				break;
		}

		if ($adapter_obj !== null) {
			return new Filesystem($adapter_obj);
		}
		return null;
	}

	/**
	 * Get a Gaufrette Filesystem for a given adapter, adapter config and path
	 * 
	 * @param string $adapter adapter to load
	 * @param Array $adapter_config to use
	 * @param string $path base path to load the filesystem adapter at
	 * @param Mixed $content content to load into the filesystem (mainly used for the "memory" adapter
	 * @param Array $options options to give to the filesystem
	 * @return FileSystem
	 */
	function getSpecificFilesystemWithCustomAdapter($adapter = 'local', $adapter_config = null, $path = null, $content = null, $options = []) {
		$adapter_obj = null;
		switch ($adapter) {
			case "local":
				$adapter_obj = new LocalAdapter($path, true);
				break;
			case "memory":
				$adapter_obj = new InMemoryAdapter(array(basename($path) => $content));
				break;
			case "s3":
				$config_options = $adapter_config['options'];
				$config_options = array_replace(is_array($config_options) ? $config_options : [], ["directory" => $path], $options);
				$client = S3Client::factory(["key" => $adapter_config['key'], "secret" => $adapter_config['secret']]);
				$adapter_obj = new AwsS3($client, $adapter_config['bucket'], is_array($config_options) ? $config_options : []);
				break;
			case "dropbox":
				if (!function_exists("oauth")) {
					$this->w->Log->setLogger("FILE_SERVICE")->error("The Dropbox API requires the oAuth extension to be installed.");
					return null;
				}
				$app_id = $adapter_config['app_id'];
				if (!empty($app_id)) {
					$adapter_obj = new \Gaufrette\Adapter\Dropbox(new Dropbox_API(new Dropbox_OAuth_PHP($app_id, '')));
				} else {
					$this->w->Log->setLogger("FILE_SERVICE")->error("Dropbox adapter requested but no app ID has been provided in the config.");
				}
				break;
		}

		if ($adapter_obj !== null) {
			return new Filesystem($adapter_obj);
		}
		return null;
	}
	
	/**
	 * Register a gaufrette stream wrapper
	 * 
	 * @param \Gaufrette\Filesystem
	 * @return null
	 */	
	function registerStreamWrapper($filesystem = null) {
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
	 * @return string html image tag
	 */	
	function getImg($path) {
		$file = FILE_ROOT . $path;
		if (!file_exists($file))
			return null;

		list($width, $height, $type, $attr) = getimagesize($file);

		$tag = "<img src='" . WEBROOT . "/file/path/" . $path . "' border='0' " . $attr . " />";
		return $tag;
	}

	/**
	 * Create a HTML image tag for a thumbnail of the image specified by $path
	 * 
	 * @param string $path image file path
	 * @return string thumbnail image url
	 */	
	function getThumbImg($path) {
		$file = FILE_ROOT . $path;
		if (!file_exists($file))
			return $path . " does not exist.";

		list($width, $height, $type, $attr) = getimagesize($file);

		$tag = "<img src='" . WEBROOT . "/file/thumb/" . $path . "' height='" . self::$_thumb_height . "' width='" . self::$_thumb_width . "' />";
		return $tag;
	}
	
	/**
	 * Check if an attachment is an image
	 * 
	 * @param string $path image file path
	 * @return bool
	 */	
	function isImage($path) {
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
	 * @return string URL to download file
	 */	
	function getDownloadUrl($path) {
		return WEBROOT . "/file/path/" . $path;
	}

	/**
	 * Lookup the attachments for a given object
	 * 
	 * @param Mixed $objectOrTable
	 * @param Mixed $id
	 * @return string
	 */	
	function getAttachmentsFileList($objectOrTable, $id = null) {
		$attachments = $this->getAttachments($objectOrTable, $id);
                
		if (!empty($attachments)) {
			$pluck = array();
			foreach ($attachments as $attachment) {
				$file_path = $attachment->getFilePath();

				if ($file_path[strlen($file_path) - 1] !== '/') {
					$file_path .= '/';
				}
                
                $pluck[] = realpath($file_path . $attachment->filename);
			}
			return $pluck;
		}
		return array();
	}
	
	/**
	 * Lookup the attachments for a given object
	 * 
	 * @param Mixed $objectOrTable
	 * @param Mixed $id
	 * @return Attachment
	 */	
	function getAttachments($objectOrTable, $id = null) {
		$table = '';
		if (is_scalar($objectOrTable)) {
			$table = $objectOrTable;
		} elseif (is_a($objectOrTable, "DbObject")) {
			$table = $objectOrTable->getDbTableName();
			$id = $objectOrTable->id;
		}
		if (!empty($table) && !empty($id)) {
			// $rows = $this->_db->get("attachment")->where("parent_table", $table)->and("parent_id", $id)->and("is_deleted", 0)->fetch_all();
			// return $this->fillObjects("Attachment", $rows);
			return $this->getObjects('Attachment', ['parent_table'=> $table, 'parent_id' => $id, 'is_deleted' => 0]);
		}
		return null;
	}

	function getAttachmentsForAdapter($adapter) {
		if (Config::get('file.adapters.' . $adapter) !== null) {
			return $this->getObjects('Attachment', ['adapter' => $adapter, 'is_deleted' => 0]);
		}
	}
	
	/**
	 * Counts attachments for a given object/table and id
	 * 
	 * @param Mixed $objectOrTable
	 * @param int (option) $id
	 * @return int
	 */
	function countAttachments($objectOrTable, $id = null) {
		if (is_scalar($objectOrTable)) {
			$table = $objectOrTable;
		} elseif (is_a($objectOrTable, "DbObject")) {
			$table = $objectOrTable->getDbTableName();
			$id = $objectOrTable->id;
		}
		
		if ($table && $id) {
			return $this->_db->get("attachment")->where("parent_table", $table)->and("parent_id", $id)->and("is_deleted", 0)->count();
		}
		
		return 0;
	}
	
	/**
	 * Counts attachments for a given object/table and id
	 * 
	 * @param Mixed $objectOrTable
	 * @param int (option) $id
	 * @return int
	 */
	function countAttachmentsForUser($object, $user) {
		if (empty($object) || empty($user) || !is_a($object, "DbObject")) {
			return 0;
		}
		
		return $this->_db->get("attachment")->where('creator_id', $user->id)
				->and("parent_table", $object->getDbTableName())->and("parent_id", $object->id)->and("is_deleted", 0)->count();
	}
	
	/**
	 * Load a single attachment
	 * 
	 * @param Mixed $id attachment ID
	 * @return Attachment
	 */	
	function getAttachment($id) {
		return $this->getObject("Attachment", $id);
	}

	/**
	 * Move an uploaded file from the temp location
	 * to
	 *  /files/attachments/<attachTable>/<year>/<month>/<day>/<attachId>/<filename>
	 * 
	 * and create an Attachment record.
	 * 
	 * @param <type> $filename
	 * @param <type> $attachTable
	 * @param <type> $attachId
	 * @param <type> $title
	 * @param <type> $description
	 * @return Mixed the id of the attachment object or null
	 */
	function uploadAttachment($requestkey, $parentObject, $title = null, $description = null, $type_code = null) {
            
                if (empty($_POST[$requestkey]) && (empty($_FILES[$requestkey]) || $_FILES[$requestkey]['size'] <= 0)) {
                    return false;
                }
            
            
                if (!is_a($parentObject, "DbObject")) {
			$this->w->error("Parent not found.");
		}

		$replace_empty = array("..", "'", '"', ",", "\\", "/");
		$replace_underscore = array(" ", "&", "+", "$", "?", "|", "%", "@", "#", "(", ")", "{", "}", "[", "]", ",", ";", ":");
		
		//Check for posted content
		if(!empty($_POST[$requestkey]) && empty($_FILES[$requestkey])) {
			$filename = str_replace($replace_underscore, "_", str_replace($replace_empty, "", $_POST[$requestkey]));
		} else {
			$filename = str_replace($replace_underscore, "_", str_replace($replace_empty, "", basename($_FILES[$requestkey]['name'])));
		}

		$att = new Attachment($this->w);
		$att->filename = $filename;
		$att->fullpath = null;
		$att->parent_table = $parentObject->getDbTableName();
		$att->parent_id = $parentObject->id;
		$att->title = (!empty($title) ? $title : $filename);
		$att->description = $description;
		$att->type_code = $type_code;
		$att->insert();

		$filesystemPath = "attachments/" . $parentObject->getDbTableName() . '/' . date('Y/m/d') . '/' . $parentObject->id . '/';
		$filesystem = $this->getFilesystem($this->getFilePath($filesystemPath));
		if (empty($filesystem)) {
			$this->w->Log->setLogger("FILE_SERVICE")->error("Cannot save file, no filesystem returned");
			return null;
		}
		
		$file = new File($filename, $filesystem);
		
		$att->adapter = $this->getActiveAdapter();
		$att->fullpath = str_replace(FILE_ROOT, "", $filesystemPath . $filename);
		
		//Check for posted content
		if(!empty($_POST[$requestkey])) {
			preg_match('%data:(.*);base%', substr($_POST[$requestkey], 0, 25), $mime);
			$data = substr($_POST[$requestkey], strpos($_POST[$requestkey], ",") + 1);
			$mime_type = $mime[1];
			$content = base64_decode($data);
            $file->setContent($content, ['contentType' => $mime_type]); 
		} else {
			$content = file_get_contents($_FILES[$requestkey]['tmp_name']);
			$file->setContent($content);
			switch($att->adapter) {
				case "local":
					$mime_type = $this->w->getMimetype(FILE_ROOT . $att->fullpath);
                    break;
				default:
					$mime_type = $this->w->getMimetypeFromString($content);
                    
			}
		}
		
		
		$att->mimetype = $mime_type;		
		$att->update();
		return $att->id;
	}

	/**
	 * Uploads multiple attachments at once (Using the Html::multiFileUpload function
	 *
	 *  Stores in /uploads/attachments/<ObjectTableName>/<year>/<month>/<day>/<attachId>/<filename>
	 *
	 * @param string $requestKey
	 * @param DbObject $parentObject
	 * @param Array $titles
	 * @param Array $descriptions
	 * @param Array $type_codes
	 * @return bool if upload was successful
	 */
	function uploadMultiAttachment($requestkey, $parentObject, $titles = null, $descriptions = null, $type_codes = null) {
		if (!is_a($parentObject, "DbObject")) {
			$this->w->error("Parent object not found.");
			return false;
		}

		$rpl_nil = array("..", "'", '"', ",", "\\", "/");
		$rpl_ws = array(" ", "&", "+", "$", "?", "|", "%", "@", "#", "(", ")", "{", "}", "[", "]", ",", ";", ":");

		if (!empty($_FILES[$requestkey]['name']) && is_array($_FILES[$requestkey]['name'])) {
			$file_index = 0;
			foreach ($_FILES[$requestkey]['name'] as $FILE_filename) {
				// Files can be empty
				if (!empty($FILE_filename['file'])) {
					$filename = str_replace($rpl_ws, "_", str_replace($rpl_nil, "", basename($FILE_filename['file'])));

					$att = new Attachment($this->w);
					$att->filename = $filename;
					$att->fullpath = null;
					$att->parent_table = $parentObject->getDbTableName();
					$att->parent_id = $parentObject->id;
					$att->title = (!empty($titles[$file_index]) ? $titles[$file_index] : '');
					$att->description = (!empty($descriptions[$file_index]) ? $descriptions[$file_index] : '');
					$att->type_code = (!empty($type_codes) ? $type_codes[$file_index] : '');
					$att->insert();

					$filesystemPath = FILE_ROOT . "attachments/" . $parentObject->getDbTableName() . '/' . date('Y/m/d') . '/' . $att->id . '/';
					$filesystem = $this->getFilesystem($filesystemPath);
					$file = new File($filename, $filesystem);
					$file->setContent(file_get_contents($_FILES[$requestkey]['tmp_name'][$file_index]['file']));

					$att->fullpath = str_replace(FILE_ROOT, "", $filesystemPath . $filename);
					$att->update();
				}

				$file_index++;
			}
		}

		return true;
	}

	/**
	 * Save an attachment and create a file based on content passed as a parameter
	 * 
	 * @param DbObject $object object to save content to
	 * @param string $content file content
	 * @param Mixed $name
	 * @param Mixed $type_code
	 * @param Mixed $content_type
	 * @return int Attachment ID
	 */	
	function saveFileContent($object, $content, $name = null, $type_code = null, $content_type = null) {

		$filename = (!empty($name) ? $name : (str_replace(".", "", microtime()) . getFileExtension($content_type)));

		$filesystemPath = FILE_ROOT . "attachments/" . $object->getDbTableName() . '/' . date('Y/m/d') . '/' . $object->id . '/';

		$filesystem = $this->getFilesystem($filesystemPath);
		$file = new File($filename, $filesystem);
		$file->setContent($content);

		$att = new Attachment($this->w);
		$att->filename = $filename;
		$att->fullpath = str_replace(FILE_ROOT, "", $this->getFilePath($filesystemPath) . (substr($this->getFilePath($filesystemPath), -1) !== '/' ? '/' : '') . $att->filename);
		$att->parent_table = $object->getDbTableName();
		$att->parent_id = $object->id;
		$att->title = $filename;
		$att->type_code = $type_code;
		$att->mimetype = $content_type;
//                $att->modifier_user_id = $this->w->Auth->user()->id;
		$att->insert();

		return $att->id;
	}
	
	/**
	 * Get the attachment types for a given object type
	 * 
	 * @param DbObejct $object
	 * @return Array<AttachmentType>
	 */	
	function getAttachmentTypesForObject($object) {
		return $this->getObjects("AttachmentType", array("table_name" => $object->getDbTableName(), "is_active" => '1'));
	}

	/**
	 * Render a template showing an attachment
	 * 
	 * @param DbObject $object
	 * @param string $backUrl
	 * @return string  
	 */	
	function getImageAttachmentTemplateForObject($object, $backUrl) {
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

}
