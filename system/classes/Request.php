<?php

/**
 * The Request class is a lightweight wrapper around the $_REQUEST superglobal
 * containing static methods to aid in fetching from $_REQUEST in a safe way.
 */
class Request
{
    /**
     * Returns the value that is tied to the $key parameter as an int. If the value is
     * not scalar or doesn't exist the $default parameter will be returned instead.
     *
     * @param string $key
     * @param integer|null $default
     * @return integer|null
     */
    public static function int(string $key, ?int $default = null): ?int
    {
        if (!array_key_exists($key, $_REQUEST) || !is_scalar($_REQUEST[$key])) {
            return $default;
        }

        return intval($_REQUEST[$key]);
    }

    /**
     * Returns the value that is tied to the $key parameter as a float. If the value is
     * not scalar or doesn't exist the $default parameter will be returned instead.
     *
     * @param string $key
     * @param float|null $default
     * @return float|null
     */
    public static function float(string $key, ?float $default = null): ?float
    {
        if (!array_key_exists($key, $_REQUEST) || !is_scalar($_REQUEST[$key])) {
            return $default;
        }

        return floatval($_REQUEST[$key]);
    }

    /**
     * Returns the value that is tied to the $key parameter as a bool. If the value is
     * not scalar or doesn't exist the $default parameter will be returned instead.
     *
     * @param string $key
     * @param boolean|null $default
     * @return boolean|null
     */
    public static function bool(string $key, ?bool $default = null): ?bool
    {
        if (!array_key_exists($key, $_REQUEST) || !is_scalar($_REQUEST[$key])) {
            return $default;
        }

        return boolval($_REQUEST[$key]);
    }

    /**
     * Returns the value that is tied to the $key parameter as a string. If the value is
     * not scalar or doesn't exist the $default parameter will be returned instead.
     *
     * @param string $key
     * @param string|null $default
     * @return string|null
     */
    public static function string(string $key, ?string $default = null): ?string
    {
        if (!array_key_exists($key, $_REQUEST) || !is_scalar($_REQUEST[$key])) {
            return !empty($default) ? trim($default) : '';
        }

        return trim(strval($_REQUEST[$key]));
    }

    /**
     * Returns the value that is tied to the $key parameter as an array. If the value is
     * no an array or doesn't exist the $default parameter will be returned instead.
     *
     * @param string $key
     * @param array $default
     * @return array
     */
    public static function array(string $key, array $default = []): array
    {
        if (!array_key_exists($key, $_REQUEST) || !is_array($_REQUEST[$key])) {
            return $default;
        }

        return $_REQUEST[$key];
    }

    /**
     * Returns the value that is tied to the $key parameter. If the value doesn't
     * exist the $default parameter will be returned instead.
     *
     * @todo PHP 8 accepts mixed return type hinting. Once using PHP 8 add
     * ': mixed' as a type hint for the return type and ?mixed as the $default
     * parameter type.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function mixed(string $key, $default = null)
    {
        if (!array_key_exists($key, $_REQUEST)) {
            return is_string($default) ? trim($default) : $default;
        }

        return is_string($_REQUEST[$key]) ? trim($_REQUEST[$key]) : $_REQUEST[$key];
    }

    /**
     * Checks if the $key parameter exists in the $_REQUEST superglobal.
     *
     * @param string $key
     * @return boolean
     */
    public static function has(string $key): bool
    {
        return array_key_exists($key, $_REQUEST);
    }

    /**
     * Checks if any of the keys in the $keys parameter exists in the $_REQUEST superglobal.
     *
     * @param string ...$keys
     * @return boolean
     */
    public static function hasAny(string ...$keys): bool
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $_REQUEST)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks if all of the keys in the $keys parameter exist in the $_REQUEST superglobal.
     *
     * @param string ...$keys
     * @return boolean
     */
    public static function hasAll(string ...$keys): bool
    {
        foreach ($keys as $key) {
            if (!array_key_exists($key, $_REQUEST)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns an array of all the parameters in the $_REQUEST superglobal. If a
     * predicate is supplied, only the parameters that pass the predicate will be
     * returned.
     * 
     * @param callable|null $predicate
     * @return array
     */
    public static function params(callable $predicate = null): array
    {
        if (empty($predicate)) {
            return $_REQUEST;
        }

        $params = [];
        foreach ($_REQUEST as $key => $value) {
            if ($predicate($key, $value)) {
                $params[$key] = $value;
            }
        }

        return $params;
    }
}
