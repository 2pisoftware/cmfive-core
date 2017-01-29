<?php
function path_GET(Web &$w) {
	// make sure we secure from /../../etc/passwd attacks!!
	$filename = str_replace("..", "", FILE_ROOT.$w->getPath());
	$w->sendFile($filename);
}
