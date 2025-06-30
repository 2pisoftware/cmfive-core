<?php

/**
 * This class is designed to manage page traversal (history) by storing values
 * in the $_SESSION Calling History::add($name) will add that name and a
 * timestamp to an array in session with the current url path as the key. If you
 * also provide an object as the third parameter, the added history object will
 * be automatically removed from the breadcrumbs when you delete the
 * aforementioned object.
 *
 * NOTE: this means that any GET/POST parameters CANNOT be stored along with the path
 *
 * @author Adam Buckley
 */

class History
{
    // Storage array
    private static $cookie_key = 'cmfive_history';

    /**
     * This function adds a history value to the SESSION
     * @param string $name
     */
    public static function add($name, Web $w = null, $object = null)
    {
        // Sanitise the string
        if (!empty($name)) {
            $name = trim(htmlspecialchars(strip_tags($name)));
        }

        if (!empty($_SESSION[self::$cookie_key])) {
            // Get history form session and sort ($register is by reference)
            uasort($_SESSION[self::$cookie_key], ['History', 'sort']);
        } else {
            $_SESSION[self::$cookie_key] = [];
        }

        // Prepend module name to current name
        if (!empty($w)) {
            $name = $w->_module . (!empty($name) ? ': ' . $name : '');
        }

        // Store array in SESSION
        $array = ['name' => $name, 'time' => time()];
        if (!empty($object)) {
            $array['object_class'] = get_class($object);
            $array['object_id'] = $object->id;
        }

        // Sometimes the slash is on, sometimes its not, which creates multiple
        // entries for the same place, solution is to strip the end slash and "/index" if it exists
        $_SESSION[self::$cookie_key][preg_replace("/\/index$/", '', rtrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/'))] = $array;
    }

    /**
     * This function will attempt to return a $length amount of elements
     * out of the History array by $key (key optional)
     *
     * @param string $key (optional)
     * @param int $length (optional)
     * @return array the history
     */
    public static function get($key = null, $length = 0)
    {
        // Load cookie storage into array to be manipulated
        if (empty($_SESSION[self::$cookie_key])) {
            return null;
        }

        // Get history form cookie and sort
        $history = $_SESSION[self::$cookie_key];
        uasort($history, ['History', 'sort']);

        // Return history with empty key
        if (empty($key)) {
            if (0 < $length) {
                // Return last $length elements (http://stackoverflow.com/questions/5468912/php-get-the-last-3-elements-of-an-array)
                return array_slice($history, $length * -1, $length, true);
            }
            return $history;
        }

        if (empty($history[$key])) {
            return null;
        } else {
            return $history[$key];
        }
    }

    /**
     * Will attempt to remove objects that have matching properties from History
     *
     * @param type $object
     * @return null
     */
    public static function remove($object = null)
    {
        // Load cookie storage into array to be manipulated
        if (empty($_SESSION[self::$cookie_key]) || empty($object)
            || !is_a($object, "DbObject") || !property_exists($object, "id")
        ) {
            return;
        }

        // Get history form cookie and sort
        $history = $_SESSION[self::$cookie_key];

        $class = get_class($object);
        $id = $object->id;

        foreach ($history as $path => $history_entry) {
            if (array_key_exists("object_class", $history_entry) && array_key_exists("object_id", $history_entry)) {
                if ($history_entry['object_class'] == $class && $history_entry['object_id'] == $id) {
                    unset($_SESSION[self::$cookie_key][$path]);
                }
            }
        }
    }

    /**
     * This is a sort function for a History entry
     *
     * @param Array $a
     * @param Array $b
     * @return int comparison
     */
    private static function sort($a, $b)
    {
        return $b['time'] - $a['time'];
    }

    /**
     * This function clears history
     */
    public static function clear()
    {
        $_SESSION[self::$cookie_key] = [];
    }

    // Sanity checking
    public static function dump()
    {
        var_dump($_SESSION[self::$cookie_key]);
    }
}
