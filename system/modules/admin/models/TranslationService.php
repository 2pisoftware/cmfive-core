<?php
/**
From http://ottopress.com/2012/internationalization-youre-probably-doing-it-wrong/

- single quoted no variables
- translate phrases not words
- translation parameters (use sprintf format)
- no html
 */
class TranslationService extends DbService {

	function translate($key, $parameters = []) {

		$lang = Config::get('system.language');
		$module = $this->w->currentModule();
		$path = $this->w->getModuleDir();
		$submodule = $this->w->currentSubModule();
		$action = $this->w->currentAction();

		// Load translation file for this module
		if (empty(Config::get("translations." . $module))) {
			$filename = "translations.php";
			if (strlen(trim($lang)) > 0 && file_exists($path . "translations." . $lang . ".php")) {
				$filename = "translations." . $lang . ".php";
			}
			include_once $path . $filename;
		}

		// lookup/transform value
		$result = '';
		if (!empty($submodule)) {
			// key with full path to action
			if (!empty(Config::get("translations." . $module . "." . $submodule . "." . $action . "." . $key))) {
				$result = Config::get("translations." . $module . "." . $submodule . "." . $action . "." . $key);
				// key with module path to action
			} else if (!empty(Config::get("translations." . $module . "." . $action . "." . $key))) {
				$result = Config::get("translations." . $module . "." . $action . "." . $key);
				// path to submodule
			} else if (!empty(Config::get("translations." . $module . "." . $submodule . "." . $key))) {
				$result = Config::get("translations." . $module . "." . $submodule . "." . $key);
				// path to module
			} else if (!empty(Config::get("translations." . $module . "." . $key))) {
				$result = Config::get("translations." . $module . "." . $key);
			}
		} else {
			// key with full path to action
			if (!empty(Config::get("translations." . $module . "." . $action . "." . $key))) {
				$result = Config::get("translations." . $module . "." . $action . "." . $key);
				// path to module
			} else if (!empty(Config::get("translations." . $module . "." . $key))) {
				$result = Config::get("translations." . $module . "." . $key);
			}
		}

		return sprintf($result, $parameters);
	}

}
