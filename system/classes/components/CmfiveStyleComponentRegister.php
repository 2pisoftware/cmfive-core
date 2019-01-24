<?php

class CmfiveStyleComponentRegister extends CmfiveComponentRegister {

	protected static $_register = [];
	
	public static function outputStyles() {
		usort(static::$_register, ['CmfiveComponentRegister', 'compareWeights']);

		array_map(function($style) {
			echo $style->_include() . "\n";
		}, static::getComponents() ? : []);
	}

}