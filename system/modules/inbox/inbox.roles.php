<?php 

function role_inbox_reader_allowed(Web $w, $path) {
    return $w->checkUrl($path, "inbox", null, "index") ||
           $w->checkUrl($path, "inbox", null, "view") ||
           $w->checkUrl($path, "inbox", null, "allread") ||
           $w->checkUrl($path, "inbox", null, "archive") ||
           $w->checkUrl($path, "inbox", null, "delete") ||
           $w->checkUrl($path, "inbox", null, "deleteforever") ||
           $w->checkUrl($path, "inbox", null, "showarchive") ||
           $w->checkUrl($path, "inbox", null, "trash") ||
           $w->checkUrl($path, "inbox", null, "read");
}

function role_inbox_sender_allowed(Web $w, $path) {
	return $w->checkUrl($path, "inbox", null, '*'); //preg_match("/inbox(-.*)?\//",$path);
}
