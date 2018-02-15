<?php

class CmfiveScriptComponentRegister extends CmfiveComponentRegister {

	protected static $_register = [];

	public static function outputScripts() {
		array_map(function($script) {
			echo $script->_include() . "\n";
		}, static::getComponents() ? : []);
	}

}