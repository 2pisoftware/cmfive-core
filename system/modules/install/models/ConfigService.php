<?php

define("INSTALLER_CONFIG_FILE", "config.installer.php");
define("INSTALLER_TEMPLATE_CONFIG_FILE", "system/modules/install/assets/config.tpl.php");
define("PROJECT_CONFIG_FILE", "config.php");

class ConfigService extends DbService {
	
	/**
	 * Will write data to the twig config template where variables match
	 * 
	 * @param <Array> $data
	 * @return int or FALSE
	 */
	public static function saveConfigData($data) {
		$template_path = INSTALLER_CONFIG_FILE;
		require_once 'Twig-1.13.2/lib/Twig/Autoloader.php';
		Twig_Autoloader::register();

		$template = null;
		if (file_exists($template_path)) {
			$dir = dirname($template_path);
			$loader = new Twig_Loader_Filesystem($dir);
			$template = str_replace($dir . DS, "", $template_path);
		} else {
			$loader = new Twig_Loader_String();
			$template = $template_path;
		}
		
		// Render data in config
		$twig = new Twig_Environment($loader, array('debug' => true));
		$twig->addExtension(new Twig_Extension_Debug());

		$config_template = $twig->loadTemplate($template);
		$result_config = $config_template->render($data);
		
		return file_put_contents($template_path, $result_config);
	}
	
	/**
	 * Will put the final config in the project
	 * 
	 * @return null
	 */
	public static function writeConfigToProject() {
		copy(INSTALLER_CONFIG_FILE, PROJECT_CONFIG_FILE);
		file_put_contents(PROJECT_CONFIG_FILE, "<?php\n\n" . file_get_contents(PROJECT_CONFIG_FILE));
		unlink(INSTALLER_CONFIG_FILE);
	}
	
	/**
	 * Puts the template config file from the assets folder into the project directory
	 * only if it doesn't already exist
	 * 
	 * @return null
	 */
	public static function initConfigFile() {
		if (!file_exists(INSTALLER_CONFIG_FILE)) {
			copy(INSTALLER_TEMPLATE_CONFIG_FILE, INSTALLER_CONFIG_FILE);
		}
	}

	
	/**
	 * Puts the template config file from the assets folder into the project directory
	 * 
	 * @return null
	 */
	public static function resetConfigFile() {
		copy(INSTALLER_TEMPLATE_CONFIG_FILE, INSTALLER_CONFIG_FILE);
	}
	
}