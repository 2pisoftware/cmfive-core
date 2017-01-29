<?php
function atfile_GET(Web &$w) {
	$w->setLayout(null);
	list($id) = $w->pathMatch();
	
	$attachment = $w->File->getAttachment($id);
	if (!empty($attachment) && $attachment->exists()) {
            //check if no user logged in, is attachment public
            if (!$w->auth->loggedIn() && !$attachment->is_public) {
                return;
            }
            $attachment->displayContent();
	} else {
		$w->header("HTTP/1.1 404 Not Found");
		$w->notFoundPage();
	}
}
