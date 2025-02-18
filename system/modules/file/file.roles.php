<?php
/*****************************************
 * Role files for the file module
 ****************************************/

/*****************************************
 * Check if the user with the file_upload role is allowed access to this path
 * by comparison to a number of paths allowed for this role.
 * @return boolean
 ****************************************/
function role_file_upload_allowed(Web $w,$path) {
		return $w->checkUrl($path, "file", null, "index") ||
			$w->checkUrl($path, "file", null, "attach") ||
			$w->checkUrl($path, "file", null, "new") ||
			$w->checkUrl($path, "file", null, "edit") ||
			$w->checkUrl($path, "file", null, "delete") ||
			$w->checkUrl($path, "file", "attachment", "ajaxAddAttachment") ||
            $w->checkUrl($path, "file", null, "ajax_multipart") ||
            $w->checkUrl($path, "file", "multipart", null);
}
/*****************************************
 * Check if the user with the file_download role is allowed access to this path
 * by comparison to a number of paths allowed for this role.
 * @return boolean
 ****************************************/
function role_file_download_allowed(Web $w,$path) {
		return $w->checkUrl($path, "file", null, "index") ||
			$w->checkUrl($path, "file", null, "path") ||
			$w->checkUrl($path, "file", null, "atthumb") ||
			$w->checkUrl($path, "file", null, "atdel") ||
			$w->checkUrl($path, "file", null, "printview") ||
			$w->checkUrl($path, "file", null, "atfile") ||
			$w->checkUrl($path, "file", null, "view") ||
			$w->checkUrl($path, "file", null, "atdownload") ||
			$w->checkUrl($path, "file", null, "thumb");
}
