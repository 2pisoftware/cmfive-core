<?php

abstract class SuperGlobal
{
    protected static $array;

    protected static function int(string $key, ?int $default = null): ?int
    {
        if (!array_key_exists($key, self::$array) || !is_int(self::$array[$key])) {
            return trim($default);
        }

        return trim(self::$array[$key]);
    }

    protected static function float(string $key, ?float $default = null): ?float
    {
        if (!array_key_exists($key, self::$array) || !is_float(self::$array[$key])) {
            return trim($default);
        }

        return trim(self::$array[$key]);
    }

    protected static function bool(string $key, ?bool $default = null): ?bool
    {
        if (!array_key_exists($key, self::$array) || !is_bool(self::$array[$key])) {
            return trim($default);
        }

        return trim(self::$array[$key]);
    }

    protected static function string(string $key, ?string $default = null): ?string
    {
        if (!array_key_exists($key, self::$array) || !is_string(self::$array[$key])) {
            return trim($default);
        }

        return trim(self::$array[$key]);
    }

    protected static function mixed(string $key, $default = null)
    {
        if (!array_key_exists($key, self::$array)) {
            return trim($default);
        }

        return trim(self::$array[$key]);
    }
}
