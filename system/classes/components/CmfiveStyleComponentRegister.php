<?php

class CmfiveStyleComponentRegister extends CmfiveComponentRegister {

	protected static $_register = [];
	
	public static function outputStyles() {
		array_map(function($style) {
			echo $style->_include() . "\n";
		}, static::getComponents() ? : []);
	}

}