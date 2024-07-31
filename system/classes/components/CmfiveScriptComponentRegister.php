<?php

class CmfiveScriptComponentRegister extends CmfiveComponentRegister {

	protected static $_register = [];

	public static function outputScripts() {
		usort(static::$_register, ['CmfiveComponentRegister', 'compareWeights']);

		array_map(function($script) {
			echo $script->_include() . "\n";
		}, static::getComponents() ? : []);
	}

    public static function requireVue3()
    {
        static::registerComponent('vue3', new CmfiveScriptComponent('/system/templates/base/node_modules/vue3/dist/vue.global.prod.js'));
        if (array_key_exists('vue2', static::$_register)) {
            unset(static::$_register['vue2']);
        }
    }

    public static function requireVue2()
    {
        static::registerComponent('vue2', new CmfiveScriptComponent('/system/templates/js/vue.js'));
    }

}