<?php

/**
 * The Request class is a lightweight wrapper around the $_REQUEST superglobal
 * containing static methods to aid in fetching from $_REQUEST in a safe way.
 */
class Request
{
    /**
     * Returns the int value that is tied to the $key parameter. If the value is
     * not an int or doesn't exist the $default parameter will be returned instead.
     *
     * @param string $key
     * @param integer|null $default
     * @return integer|null
     */
    public static function int(string $key, ?int $default = null): ?int
    {
        if (!array_key_exists($key, $_REQUEST) || !is_int($_REQUEST[$key])) {
            return trim($default);
        }

        return trim($_REQUEST[$key]);
    }

    /**
     * Returns the float value that is tied to the $key parameter. If the value is
     * no a float or doesn't exist the $default parameter will be returned instead.
     *
     * @param string $key
     * @param float|null $default
     * @return float|null
     */
    public static function float(string $key, ?float $default = null): ?float
    {
        if (!array_key_exists($key, $_REQUEST) || !is_float($_REQUEST[$key])) {
            return trim($default);
        }

        return trim($_REQUEST[$key]);
    }

    /**
     * Returns the bool value that is tied to the $key parameter. If the value is
     * not a bool or doesn't exist the $default parameter will be returned instead.
     *
     * @param string $key
     * @param boolean|null $default
     * @return boolean|null
     */
    public static function bool(string $key, ?bool $default = null): ?bool
    {
        if (!array_key_exists($key, $_REQUEST) || !is_bool($_REQUEST[$key])) {
            return trim($default);
        }

        return trim($_REQUEST[$key]);
    }

    /**
     * Returns the string value that is tied to the $key parameter. If the value is
     * not a string or doesn't exist the $default parameter will be returned instead.
     *
     * @param string $key
     * @param string|null $default
     * @return string|null
     */
    public static function string(string $key, ?string $default = null): ?string
    {
        if (!array_key_exists($key, $_REQUEST) || !is_string($_REQUEST[$key])) {
            return trim($default);
        }

        return trim($_REQUEST[$key]);
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
            return trim($default);
        }

        return trim($_REQUEST[$key]);
    }
}
