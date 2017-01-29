<?php

/**
 * This class is responsible for storing and accessing 
 * the configurations for each class with case sensitive keys. 
 *
 * Config getting and setting is done using the dot syntax i.e.
 *     Config::set("admin.topmenu", true);
 * Translates to:
 *     $register["admin"]["topmenu"] = true;
 * Then the inverse function also works:
 *     Config::get("admin.topmenu"); // returns true
 * 
 * If any keys aren't found in set then they are created, also the value
 * itself can be an array, of which the values within can then be subsequently
 * retrieved with the abovementioned get function and dot syntax.
 * 
 * Note: When calling Config::get, if a key is not found NULL is returned so it 
 * is important to check that condition when fetching config keys.
 * 
 * @author Adam Buckley
 */

class Config {
    
    // Storage array
    private static $register = array();
    private static $_config_cache = array();
    private static $_keys_cache;
    
    /**
     * This function will set a key in an array
     * to the value given
     *
     * @param string $key
     * @param mixed $value
     * @return null
     */
    public static function set($key, $value) {
        $exploded_key = explode('.', $key);
        if (!empty($exploded_key)) {
            $register = &self::$register;
            // Loop through each key
            foreach($exploded_key as $ekey) {
                if (is_array($register) && !array_key_exists($ekey, $register)) {
                    $register[$ekey] = array();
                }
                $register = &$register[$ekey];
            }
            $register = $value;
        }
    }
    
    /**
     * This function will attempt to return a
     * key out of the array
     *
     * @param string $key
     * @return Mixed the value
     */
    public static function get($key, $default = null) {
        if(!empty(self::$_config_cache[$key])) {
           return self::$_config_cache[$key];
        }
        $exploded_key = explode('.', $key);
        // Copy the register for processing
        $value = &self::$register;
        if (!empty($exploded_key)) {
			$i = 0;
            // Loop through each key
			while(isset($exploded_key[$i]) && isset($value[$exploded_key[$i]])) {
				$value = &$value[$exploded_key[$i]];
				$i++;
			}
			if($i !== count($exploded_key)) {
				self::$_config_cache[$key] = null;
			} else {
				self::$_config_cache[$key] = &$value;
			}
            return self::$_config_cache[$key];
        }
		self::$_config_cache[$key] = null;
        return !is_null($default) ? $default : null;
    }
    
    /**
     * A small helper function for web to get the list of keys (modules)
     * 
     * @return array
     */
    public static function keys($getAll = false) {
        if ($getAll === true) {
            return array_keys(self::$register);
        }
        if(!empty(self::$_keys_cache)) {
            return self::$_keys_cache;
        }
        $required = array("topmenu", "active", "path");
        $req_count = count($required);
        $modules = array_filter(self::$register, function($var) use ($required, $req_count) {
            return ($req_count === count(array_intersect_key($var, array_flip($required))));
        });
        self::$_keys_cache = array_keys($modules);
        return array_keys($modules);
    }
    
    /**
     * A function to append a value to an array, if target is not an array this function 
     * will overwrite the current targets value so use with caution!
     * 
     * If value to write is also an array, this function will merge the target with the value
     * 
     * @param string $key
     * @param mixed $value
     * @return null
     */
    public static function append($key, $value) {
        $target_value = self::get($key);
        
        // If target isn't set then set it
        if (empty($target_value)) {
            if (is_array($value)) {
                self::set($key, $value);
            } else {
                self::set($key, array($value));
            }
        } else {
            if (is_array($target_value)) {
                if (is_array($value)) {
                    self::set($key, array_merge($target_value, $value));
                } else {
                    $target_value[] = $value;
                    self::set($key, $target_value);
                }
            } else {
                // Overwrite target value
                if (is_array($value)) {
                    self::set($key, $value);
                } else {
                    self::set($key, array($value));
                }
            }
        }
    }
    
    // Sanity checking
    public static function dump() {
        var_dump(self::$register);
    }
    
    public static function toJson() {
    	return json_encode(self::$register);
    }
    
    public static function fromJson($string){
    	self::$register = json_decode($string,true);
    }
}
