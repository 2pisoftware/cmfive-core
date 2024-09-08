<?php

class CmfiveComponentRegister
{

    protected static $_register = [];

    public static function registerComponent($key, CmfiveComponent $details)
    {
        if (!array_key_exists($key, static::$_register)) {
            static::$_register[$key] = $details;
        }
    }

    public static function getComponents()
    {
        return static::$_register;
    }

    public static function getComponent($key)
    {
        if (array_key_exists($key, static::$_register)) {
            return static::$_register[$key];
        }

        return null;
    }

    public static function compareWeights($a, $b)
    {
        if (is_array($a)) {
            $aw = intval($a["weight"]);
        } elseif (property_exists(get_class($a), 'weight')) {
            $aw = intval($a->weight);
        }

        if (is_array($b)) {
            $bw = intval($b["weight"]);
        } elseif (property_exists(get_class($b), 'weight')) {
            $bw = intval($b->weight);
        }

        return ($aw === $bw ? 0 : ($aw < $bw ? 1 : -1));
    }
}
