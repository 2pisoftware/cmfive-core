<?php

use Leafo\ScssPhp\Compiler;

class CmfiveStyleComponent extends CmfiveComponent {

	public $_filename;
	public $_dirname;
	public $_extension = 'css';
	public $_include_paths = [];
	public $_external = false;
	public static $_allowed_extensions = ['css', 'scss'];

	public function __construct($path, $include_paths = [], $is_external = false, $props = []) {
		if (!empty($props)) {
		    foreach($props as $key => $value) {
				$this->$key = $value;
			}
		}

		$this->_external = $is_external;
		if (!$this->_external) {
			$style_path = pathinfo($path);

			if (empty($style_path['extension']) || !in_array($style_path['extension'], static::$_allowed_extensions)) {
				throw new Exception('Invalid file path given to component');
			}

			$this->_extension = $style_path['extension'];
			$this->_filename = $style_path['filename'];
			$this->_dirname = $style_path['dirname'];
			$this->_include_paths = $include_paths;
		} else {
			$this->href = $path;
		}
	}

	public function setProps(Array $props) {
		if (!empty($props)) {
		    foreach($props as $key => $value) {
				$this->$key = $value;
			}
		}

		return $this;
	}

	public function _include() {
		switch ($this->_extension) {
			case 'scss': {
				// Compile and store in cache directory
				$scss = new Compiler();
				if (!empty($this->_include_paths)) {
					foreach($this->_include_paths as $_include_path) {
						$scss->addImportPath(function($_path) use ($_include_path) {
							if (file_exists(ROOT_PATH . $_include_path . $_path)) {
								return ROOT_PATH . $_include_path . $_path;
							}

							return null;
						});
					}
				}

				try {
					$compiled_css = $scss->compile(file_get_contents(ROOT_PATH . $this->_dirname . '/' . $this->_filename . '.' . $this->_extension));
				} catch (Exception $e) {
					// Could not compile SCSS
					echo $e->getMessage();
					return;
				}
				
				if (!is_dir(ROOT_PATH . '/cache/css/')) {
					mkdir(ROOT_PATH . '/cache/css/');
					
				}
				// check if exists or make .htaccess with allow from all
				if (!file_exists(ROOT_PATH . '/cache/css/.htaccess')) {
					$access_file = fopen(ROOT_PATH . '/cache/css/.htaccess', 'w');
					fwrite($access_file, "Allow From All");
					fclose($access_file);
				}

				file_put_contents(ROOT_PATH . '/cache/css/' . $this->_filename . '.css', $compiled_css);
				$this->rel = 'stylesheet';
				$this->href = '/cache/css/' . $this->_filename . '.css';
				return parent::_include();
			}
			case 'css':
			default:
				$this->rel = 'stylesheet';
				if (!$this->_external) {
					$this->href = $this->_dirname . '/' . $this->_filename . '.' . $this->_extension;
				}
				return parent::_include();
		}
	}
}