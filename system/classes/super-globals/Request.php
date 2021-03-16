<?php

class Request implements SuperGlobal
{
    public static function int(string $key, ?int $default = null): ?int
    {
        SuperGlobal::$array = $_REQUEST;
        return SuperGlobal::int($key, $default);
    }

    public static function float(string $key, ?float $default = null): ?float
    {
        SuperGlobal::$array = $_REQUEST;
        return SuperGlobal::float($key, $default);
    }

    public static function bool(string $key, ?bool $default = null): ?bool
    {
        SuperGlobal::$array = $_REQUEST;
        return SuperGlobal::bool($key, $default);
    }

    public static function string(string $key, ?string $default = null): ?string
    {
        SuperGlobal::$array = $_REQUEST;
        return SuperGlobal::string($key, $default);
    }

    public static function mixed(string $key, $default = null)
    {
        SuperGlobal::$array = $_REQUEST;
        return SuperGlobal::mixed($key, $default);
    }
}
