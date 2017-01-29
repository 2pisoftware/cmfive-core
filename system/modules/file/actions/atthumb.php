<?php

function atthumb_GET(Web &$w) {
	list($id) = $w->pathMatch();

	// Get the attachment
	$attachment = $w->File->getAttachment($id);
	$width = $w->request("w",  FileService::$_thumb_width);
	$height = $w->request("h", FileService::$_thumb_height);
	
	header("Content-Type: image/png");
	if (!empty($attachment) && $attachment->exists()) {
	
		// Check if theres a cached thumbnail
		if ($attachment->hasCachedThumbnail()) {
			// Display cached version
			// $w->Log->debug("Serving cached thumbnail: " . $attachment->getThumbnailCachePath());
			echo file_get_contents($attachment->getThumbnailCachePath());
		} else {
			// Generate thumbnail and cache
			require_once 'phpthumb/ThumbLib.inc.php';
	
			$thumb = PhpThumbFactory::create($attachment->getContent(), [], true);
			$thumb->adaptiveResize($width, $height);
	
			// Create cached folder
			if (!is_dir(dirname($attachment->getThumbnailCachePath()))) {
				mkdir(dirname($attachment->getThumbnailCachePath()), 0755, true);
			}
			
			// Write thumbnail to cache
			file_put_contents($attachment->getThumbnailCachePath(), $thumb->getImageAsString());
			
			$thumb->show();
		}
	} else {
		header("HTTP/1.1 404 Not Found");
	}
	
	exit;
}
