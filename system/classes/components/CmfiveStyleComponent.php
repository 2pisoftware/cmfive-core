<?php

use Leafo\ScssPhp\Compiler;

class CmfiveStyleComponent extends CmfiveComponent {

	public $_filename;
	public $_dirname;
	public $_extension = 'css';
	public $_include_paths = [];
	public static $_allowed_extensions = ['css', 'scss'];

	public function __construct($path, $include_paths = []) {
		$style_path = pathinfo($path);

		if (empty($style_path['extension']) || !in_array($style_path['extension'], self::$_allowed_extensions)) {
			throw new Exception('Invalid file path given to component');
		}

		$this->_extension = $style_path['extension'];
		$this->_filename = $style_path['filename'];
		$this->_dirname = $style_path['dirname'];
		$this->_include_paths = $include_paths;
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
					$compiled_css = $scss->compile(file_get_contents(WEBROOT . $this->_dirname . '/' . $this->_filename . '.' . $this->_extension));
				} catch (Exception $e) {
					// Could not compile SCSS
					echo $e->getMessage();
					return;
				}

				if (!is_dir(ROOT_PATH . '/cache/css/')) {
					mkdir(ROOT_PATH . '/cache/css/');
				}
				file_put_contents(ROOT_PATH . '/cache/css/' . $this->_filename . '.css', $compiled_css);
				$this->rel = 'stylesheet';
				$this->href = '/cache/css/' . $this->_filename . '.css';
				return parent::_include();
			}
			case 'css':
			default:
				$this->rel = 'stylesheet';
				$this->href = $this->_dirname . '/' . $this->_filename . '.' . $this->_extension;
				return parent::_include();
		}
	}
}