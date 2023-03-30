<?php

/**
 * permanently deletes a file (unlink & DB hard delete)
 * 
 * @param Web $w
 */
function delete_GET(Web $w) {
	
	list($attachment_id) = $w->pathMatch();
	
	$redirect_url = Request::string("redirect_url", "/file");
	
	if (empty($attachment_id)) {
		$w->error("No Attachment ID given", $redirect_url);
	}
	
	$attachment = FileService::getInstance($w)->getObject("Attachment", $attachment_id);
	
	if (empty($attachment->id)) {
		$w->error("Attachment not found", $redirect_url);
	}
	
	$file = $attachment->getFile();
	
	try {
		$file->delete();
	} catch (\Gaufrette\Exception\FileNotFound $fnf) {
		LogService::getInstance($w)->setLogger("FILE_DELETE")->warning("Trying to permanently elete file but it doesn't exist in the " . $attachment->adapter . " adapter");
	} catch (RuntimeException $ex) {
		$w->error("Could not delete file: " . $ex->getMessage(), $redirect_url);
	}
	
	$attachment->delete(true);
	
	$w->msg("Attachment and file permanently deleted", $redirect_url);
			
}