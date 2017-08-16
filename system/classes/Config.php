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
 * 
 * 
 * @author Adam Buckley
 */

class Config {
    
    // Storage array
    private static $register = [];
    private static $_config_cache = [];
    private static $_keys_cache = [];
	
	private static $_use_sandbox = false;
    private static $shadow_register = [];
	private static $_shadow_config_cache = [];
	private static $_shadow_keys_cache = [];
	
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
			if (self::$_use_sandbox === true) {
				$register = &self::$shadow_register;
			}
			
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
        if(self::$_use_sandbox !== true && !empty(self::$_config_cache[$key])) {
           return self::$_config_cache[$key];
        }
		if (self::$_use_sandbox === true && !empty(self::$_shadow_config_cache[$key])) {
			return self::$shadow_config_cache[$key];
		}
		
        $exploded_key = explode('.', $key);
        // Copy the register for processing
        $value = &self::$register;
		if (self::$_use_sandbox === true) {
			$value = &self::$shadow_register;
		}
			
        if (!empty($exploded_key)) {
			$i = 0;
            // Loop through each key
			while(isset($exploded_key[$i]) && isset($value[$exploded_key[$i]])) {
				$value = &$value[$exploded_key[$i]];
				$i++;
			}
			if (self::$_use_sandbox !== true) {
				if($i !== count($exploded_key)) {
					self::$_config_cache[$key] = null;
				} else {
					self::$_config_cache[$key] = &$value;
				}
				return self::$_config_cache[$key];
			} else {
				if($i !== count($exploded_key)) {
					self::$_shadow_config_cache[$key] = null;
				} else {
					self::$_shadow_config_cache[$key] = &$value;
				}
				return self::$_shadow_config_cache[$key];
			}
        }
		
		if (self::$_use_sandbox !== true) {
			self::$_config_cache[$key] = null;
		} else {
			self::$_shadow_config_cache[$key] = null;
		}
        return !is_null($default) ? $default : null;
    }
    
    /**
     * A small helper function for web to get the list of keys (modules)
     * 
     * @return array
     */
    public static function keys($getAll = false) {
        if ($getAll === true) {
            return array_keys(self::$_use_sandbox === true ? self::$shadow_register : self::$register);
        }
        if(self::$_use_sandbox !== true && !empty(self::$_keys_cache)) {
            return self::$_keys_cache;
        }
		if(self::$_use_sandbox == true && !empty(self::$_shadow_keys_cache)) {
            return self::$_shadow_keys_cache;
        }
		
        $required = array("topmenu", "active", "path");
        $req_count = count($required);
        $modules = array_filter(self::$_use_sandbox === true ? self::$shadow_register : self::$register, function($var) use ($required, $req_count) {
            return ($req_count === count(array_intersect_key($var, array_flip($required))));
        });
		if (self::$_use_sandbox !== true) {
	        self::$_keys_cache = array_keys($modules);
		} else {
			self::$_shadow_keys_cache = array_keys($modules);
		}
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
    
	public static function enableSandbox() {
		self::$_use_sandbox = true;
	}
	
	public static function disableSandbox() {
		self::$_use_sandbox = false;
	}
	
	public static function isSandboxing() {
		return self::$_use_sandbox;
	}
	
	public static function getSandbox() {
		return self::$shadow_register;
	}
	
	public static function setSandbox($shadow_register = []) {
		self::$shadow_register = $shadow_register;
	}
	
	public static function mergeSandbox() {
		if (self::isSandboxing()) {
			if (!empty(self::$shadow_register)) {
				self::$register = array_merge(self::$register, self::$shadow_register);
			}
			self::clearSandbox();
		}
	}
	
	public static function clearSandbox() {
		self::$_shadow_config_cache = [];
		self::$_shadow_keys_cache = [];
		self::$shadow_register = [];
	}
	
    // Sanity checking
    public static function dump() {
        if (self::$_use_sandbox ) {
            var_dump(self::$shadow_register);
        } else {
            var_dump(self::$register);       
        }
    }
    
    public static function toJson() {
    	return json_encode(self::$register);
    }
    
    public static function fromJson($string){
    	self::$register = json_decode($string,true);
    }
}

/**
 * A static class to handle module dependencies. The key that is referenced is 'depends_on' and should
 * be located in the base level of the module config.
 * 
 * @author Adam Buckley <adam@2pisoftware.com>
 */
class ConfigDependencyLoader {
	private static $dependency_stack = [];
	
	/**
	 * Registers a module to be loaded
	 * 
	 * @param String $module
	 * @param Array $config
	 */
	public static function registerModule($module, $config, $include_path) {
		$stack_class = new stdClass();
		$stack_class->config = $config;
		$stack_class->module_name = $module;
		$stack_class->loaded = false;
		$stack_class->visited = false;
        $stack_class->include_path = $include_path;
		
		self::$dependency_stack[] = $stack_class;
	}
	
	/**
	 * Searches the dependency stack for a registered module
	 * 
	 * @param String $module
	 * @return Mixed registered module
	 */
	public static function getRegisteredModule($module) {
		if (!empty(self::$dependency_stack)) {
			foreach(self::$dependency_stack as $dependency) {
				if (strtolower($module) === strtolower($dependency->module_name)) {
					return $dependency;
				}
			}
		}
		return null;
	}
	
	/**
	 * Loops over the dependency stack and attempts to load each node
	 */
	public static function load() {
		if (!empty(self::$dependency_stack)) {
			// Remove - testing graph capabilities by randomising the load order of modules
			shuffle(self::$dependency_stack);
			
			foreach(self::$dependency_stack as $node) {
				self::visitNode($node);
			}
		}
	}
	
	/**
	 * Checks given node for dependencies and recursively calls this function on
	 * any dependencies specified.
	 * 
	 * Throws exceptions when function cannot find a specified dependency or if it
	 * detects a cyclic dependency definition.
	 *     e.g module a -> module b -> module a
	 * 
	 * @param stdClass $node
	 * @return null
	 * @throws Exception
	 */
	private static function visitNode($node) {
		$node->visited = true;
		if ($node->loaded === true) {
			return;
		}
		
		// Go through dependencies and load them
		if (!empty($node->config[$node->module_name]) && is_array($node->config[$node->module_name]) && array_key_exists('depends_on', $node->config[$node->module_name]) && is_array($node->config[$node->module_name]['depends_on'])) {
			foreach($node->config[$node->module_name]['depends_on'] as $_module_dependency) {
				$_dependent_node = self::getRegisteredModule($_module_dependency);
				if (empty($_dependent_node)) {
					throw new Exception($node->module_name . ' depends on ' . $_module_dependency . ' but it is not available');
				}
				
				if ($_dependent_node->loaded === true) {
					continue;
				} 
				
				if ($_dependent_node->visited === true) {
					throw new Exception('Cyclic depedency detected in ' . $node->module_name);
				}
				
				self::visitNode($_dependent_node);
			}
		}
		
		// Load module and flag
        include($node->include_path);
//		Config::enableSandbox();
//		Config::setSandbox($node->config);
//		Config::mergeSandbox();
//		Config::disableSandbox();
		$node->loaded = true;
	}
	
}