<?php

class CmfiveComponentRegister {

	protected static $_register = [];

	public static function registerComponent($key, CmfiveComponent $details) {
		if (!array_key_exists($key, static::$_register)) {
			static::$_register[$key] = $details;
		}
	}

	public static function getComponents() {
		return static::$_register;
	}

	public static function getComponent($key) {
		if (array_key_exists($key, static::$_register)) {
			return static::$_register[$key];
		}

		return null;
	}

	// public static function printComponent(string $key, array $data) -> string {
	// 	if (array_key_exists($key, self::$_register)) {
	// 		// Bind data to component here
	// 		return self::$_register[$key]->__toString($data);
	// 	}
	// }

}