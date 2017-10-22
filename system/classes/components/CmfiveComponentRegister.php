<?php

class CmfiveComponentRegister {

	private static $_register = [];

	public static function registerComponent(string $key, CmfiveComponent $details) {
		if (!array_key_exists($key, self::$_register)) {
			self::$_register[$key] = $details;
		}
	}

	public static function getComponents(): array {
		return self::$_register;
	}

	public static function getComponent($key) {
		if (array_key_exists($key, self::$_register)) {
			return self::$_register[$key];
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