<?php

class CmfiveScriptComponentRegister extends CmfiveComponentRegister {

	public static function outputScripts() {
		array_map(function($script) {
			echo $script->_include() . "\n";
		}, self::getComponents() ? : []);
	}

}