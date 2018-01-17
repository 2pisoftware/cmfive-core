<?php

class CmfiveStyleComponentRegister extends CmfiveComponentRegister {

	public static function outputStyles() {
		array_map(function($style) {
			echo $style->_include() . "\n";
		}, self::getComponents() ? : []);
	}

}