<?php
/**
* Send media files from within
* a modules help/media folder
*
* @param unknown_type $w
*/
function media_GET(Web &$w) {
	$p = $w->pathMatch("m","f");
	$m = $p['m'];
	$f = $p['f'];

	$filename = str_replace("..", "", ROOT."/".$w->getModuleDir($m).'/help/media/'.$f);
	$w->sendFile($filename);
}