<?php

interface IGlobal
{
    public static function int(string $key, ?int $default = null): ?int;
    public static function float(string $key, ?float $default = null): ?float;
    public static function bool(string $key, ?bool $default = null): ?bool;
    public static function string(string $key, ?string $default = null): ?string;
    public static function mixed(string $key, $default = null);
}

class Request implements IGlobal
{
    public static function int(string $key, ?int $default = null): ?int
    {
        if (!array_key_exists($key, $_REQUEST) || !is_int($_REQUEST[$key])) {
            return trim($default);
        }

        return trim($_REQUEST[$key]);
    }

    public static function float(string $key, ?float $default = null): ?float
    {
        if (!array_key_exists($key, $_REQUEST) || !is_float($_REQUEST[$key])) {
            return trim($default);
        }

        return trim($_REQUEST[$key]);
    }

    public static function bool(string $key, ?bool $default = null): ?bool
    {
        if (!array_key_exists($key, $_REQUEST) || !is_bool($_REQUEST[$key])) {
            return trim($default);
        }

        return trim($_REQUEST[$key]);
    }

    public static function string(string $key, ?string $default = null): ?string
    {
        if (!array_key_exists($key, $_REQUEST) || !is_string($_REQUEST[$key])) {
            return trim($default);
        }

        return trim($_REQUEST[$key]);
    }

    public static function mixed(string $key, $default = null)
    {
        if (!array_key_exists($key, $_REQUEST)) {
            return trim($default);
        }

        return trim($_REQUEST[$key]);
    }
}
