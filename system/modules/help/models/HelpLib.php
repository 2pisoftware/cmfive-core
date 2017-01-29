<?php
class HelpLib {
	
	static function extractTitle($content) {
		if (preg_match_all("/\[\[title\|(.*?)\]\]/", $content, $matches)) {
			return $matches[1][0];
		}
	}
	
	static function getHelpFilePath(Web $w,$module,$submodule,$action) {
		$help_file = $w->getModuleDir($module)."/help".($submodule ? "/".$submodule : "")."/".$action.".help";
		if (file_exists($help_file)) {
			return $help_file;
		}
		return null;
	}
}