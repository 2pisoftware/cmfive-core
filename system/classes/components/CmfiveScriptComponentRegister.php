<?php

class CmfiveScriptComponentRegister extends CmfiveComponentRegister {

	protected static $_register = [];

	public static function outputScripts() {
		usort(static::$_register, ['CmfiveComponentRegister', 'compareWeights']);

		array_map(function($script) {
			echo $script->_include() . "\n";
		}, static::getComponents() ? : []);
	}

}