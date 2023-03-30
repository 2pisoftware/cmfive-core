<?php
/**
* Show a Table of Contents by searching
* through all modules for the file
* ./help/<module>_toc.help
*
* @param \Web $w
*/
function toc_GET(Web $w) {
	foreach ($w->modules() as $h) {
		$p = HelpLib::getHelpFilePath($w, $h,null,$h."_toc");
		if ($p) {
			$tocs[$h]=$p;
		}
	}
	foreach($tocs as $module => $path) {
		if (AuthService::getInstance($w)->allowed($module.'/index')) {
			$content = file_get_contents($path);
			$title = HelpLib::extractTitle($content);
			$ul[]=Html::a(WEBROOT.'/help/view/'.$module.'/'.$module.'_toc',$title?$title:ucfirst($module));
		}
	}

	$w->ctx("ul", Html::ul($ul));
}

