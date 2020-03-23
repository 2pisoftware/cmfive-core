<?php

/**
 * Static class to generate and check CSRF tokens
 * Based on example found http://www.wikihow.com/Prevent-Cross-Site-Request-Forgery-(CSRF)-Attacks-in-PHP
 */
class CSRFException extends Exception
{
}


class CSRF
{
    private static $token_id_name = "token_id";
    private static $token_value_name = "token_value";
    private static $token_history_name = "csrf_history";


    public static function getTokenIdName()
    {
        return self::$token_id_name;
    }

    public static function getTokenValueName()
    {
        return self::$token_value_name;
    }

    public static function getTokenHistoryName()
    {
        return self::$token_history_name;
    }

    /**
     * Generates new CSRF token_id and store it in the $_SESSION.
     *
     * Returns token_id.
     *
     * @return string
     */
    public static function getTokenID()
    {
        if (!isset($_SESSION[self::$token_id_name])) {
            $_SESSION[self::$token_id_name] = self::random(10);
        }

        return $_SESSION[self::$token_id_name];
    }

    /**
     * Generates new CSRF token_value and store it in the $_SESSION.
     *
     * Returns token_value.
     *
     * @return string
     */
    public static function getTokenValue()
    {
        if (!isset($_SESSION[self::$token_value_name])) {
            $_SESSION[self::$token_value_name] = self::random(64);
        }

        return $_SESSION[self::$token_value_name];
    }

    /**
     * Regenerates the CSRF tokens, useful for preventing mulitple
     * form submissions.
     */
    public static function regenerate()
    {
        // Unset session variables
        if (isset($_SESSION[self::$token_id_name])) {
            $_SESSION[self::$token_history_name][$_SESSION[self::$token_id_name]] = $_SESSION[self::$token_value_name];
            unset($_SESSION[self::$token_id_name]);
        }
        if (isset($_SESSION[self::$token_value_name])) {
            unset($_SESSION[self::$token_value_name]);
        }

        // Create new key/value
        self::getTokenID();
        self::getTokenValue();
    }

    /**
     * Check whether or not the token value passed in $method (GET/POST..)
     * match the token stored in the $_SESSION.
     *
     * @param string $method
     *
     * @return boolean
     */
    public static function isValid($method)
    {
        $method = strtolower($method);

        // Allow get through for now
        if ($method === "get") {
            // Destory the CSRF history on GET
            if (isset($_SESSION[self::$token_history_name])) {
                unset($_SESSION[self::$token_history_name]);
            }

            return true;
        }

        // Restrict access to post
        if ($method == "post") {
            if (isset($_POST[self::getTokenID()])) {
                return ($_POST[self::getTokenID()] === self::getTokenValue());
            }

            $request_data = json_decode(file_get_contents("php://input"), true);
            if (!empty($request_data) && array_key_exists(self::getTokenID(), $request_data)) {
                return ($request_data[self::getTokenID()] === self::getTokenValue());
            }
        }

        return false;
    }

    public static function inHistory()
    {
        if (!empty($_SESSION[self::$token_history_name])) {
            foreach ($_SESSION[self::$token_history_name] as $history_key => $history_value) {
                if (isset($_POST[$history_key])) {
                    if ($_POST[$history_key] === $history_value) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    /**
     * A better random function?
     *
     * @param integer $len
     *
     * @return string
     */
    public static function random($len)
    {
        $cstrong = false;
        $bytes = openssl_random_pseudo_bytes($len, $cstrong);
        while ($cstrong == false) {
            $bytes = openssl_random_pseudo_bytes($len, $cstrong);
        }
        return bin2hex($bytes);
    }
}
