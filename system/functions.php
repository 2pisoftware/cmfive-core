<?php

/**
 * deduplicates arrays of arrays, something that array_unique can't do.
 * Given an array of arrays, this function will return an array containing only
 * unique arrays having removed any duplicate arrays.
 *
 * Thanks to http://stackoverflow.com/a/308955/1082633
 *
 * @param Array $input
 * @return multitype:
 */
function array_unique_multidimensional(Array $input)
{
    $serialized = array_map('serialize', $input);
    $unique = array_unique($serialized);
    return array_intersect_key($input, $unique);
}

/**
 * Translations shortcuts (for POT marker identification
 */

// ensure that developers use double underscore  !! requires ADP php module
//override_function('__', '$key,$context', 'throw new Exception("You must use double underscores in gettext lookups - ".$key."-".$context) ;');

// Implement gettext context
if (!function_exists('pgettext')) {
    function pgettext($context, $msgid, $domain = '')
    {
        $contextString = "{$context}\004{$msgid}";
        
        if (strlen(trim($domain)) > 0) {
            $translation = dgettext($domain, $contextString);
        } else {
            $translation = gettext($contextString);
        }

        if ($translation === $contextString) {
            return $msgid;
        } else {
            return $translation;
        }
    }

    function npgettext($context, $msgid, $msgid_plural, $num, $domain = '')
    {
        $contextString = "{$context}\004{$msgid}";
        $contextStringp = "{$context}\004{$msgid_plural}";
        
        if (strlen(trim($domain)) > 0) {
            $translation = dngettext($domain, $contextString, $contextStringp, $num);
        } else {
            $translation = ngettext($contextString, $contextStringp, $num);
        }

        if ($translation === $contextString) {
            return $msgid;
        } elseif ($translation === $contextStringp) {
            return $msgid_plural;
        } else {
            return $translation;
        }
    }
}

/**
 * Lookup translation
 */
function __($key, $context = '', $domain = '')
{
    if (strlen(trim($context)) > 0) {
        return pgettext($context, $key, $domain);
    } else {
        if (strlen(trim($domain)) > 0) {
            return dgettext($domain, $key);
        } else {
            return gettext($key);
        }
    }
}

/**
 * Echo a translation lookup
 */
function _e($key, $context = '', $domain = '')
{
    echo __($key, $context, $domain);
}

/**
 * Lookup a plural translation
 */
function _n($key1, $key2, $n, $context = '', $domain = '')
{
    if (strlen(trim($context)) > 0) {
        return npgettext($context, $key1, $key2, $n, $domain);
    } else {
        if (strlen(trim($domain)) > 0) {
            return dngettext($domain, $key1, $key2, $n);
        } else {
            return ngettext($key1, $key2, $n);
        }
    }
}

/**
 * Echo a plural translation lookup
 */
function _en($key1, $key2, $n, $context = '', $domain = '')
{
    echo _n($key1, $key2, $n, $domain, $context);
}

/**
 * Convert locale string to array of accepted versions
 *
 * @param string $base_locale
 * @return Array
 */
function getAllLocaleValues($base_locale)
{
    static $language_lookup = [
        'de_DE' => ['de_DE', 'de_DE@euro', 'deu', 'deu_deu', 'german'],
        'fr_FR' => ['fr_FR', 'fr_FR@euro', 'french'],
        'en_AU' => ['en_AU.utf8', 'en_AU', 'australian'],
    ];

    if (array_key_exists($base_locale, $language_lookup)) {
        return $language_lookup[$base_locale];
    }

    return false;
}

/**
 * Returns human readable string of given byte value
 *
 * @param int $input
 * @param int $rounding (optional, default 2)
 * @param bool $bytesValue if false will divide by 1000 instead of 1024 (optional, default true)
 * @return string
 */
function humanReadableBytes($input, $rounding = 2, $bytesValue = true)
{
    $ext = array("B", "KB", "MB", "GB", "TB");
    $barrier = 1024;
    if (!$bytesValue) {
        // If bytes value is false then we what to use 1000 (bits?)
        $barrier = 1000;
    }

    while ($input > $barrier) {
        $input /= $barrier;
        array_shift($ext);
        if ($ext[0] === end($ext)) {
            $input = round($input, $rounding);
            return "$input $ext[0]";
        }
    }
    // Round input to something reasonable
    $input = round($input, $rounding);
    return "$input $ext[0]";
}

/**
 * DEPRECATED from v3.0.0
 * Returns the extension for given content type
 *
 * @param string contentType
 * @return string extension
 */
function getFileExtension($contentType)
{
    $map = array(
        'application/pdf' => '.pdf',
        'application/zip' => '.zip',
        'image/gif' => '.gif',
        'image/jpeg' => '.jpg',
        'image/png' => '.png',
        'text/css' => '.css',
        'text/html' => '.html',
        'text/javascript' => '.js',
        'text/plain' => '.txt',
        'text/xml' => '.xml',
    );

    if (isset($map[$contentType])) {
        return $map[$contentType];
    }

    // HACKISH CATCH ALL (WHICH IN MY CASE IS
    // PREFERRED OVER THROWING AN EXCEPTION)
    $pieces = explode('/', $contentType);
    return '.' . array_pop($pieces);
}

/**
 * Small helper function to test for isset and is_numeric
 *
 * @param mixed|null var
 * @return boolean
 */
function isNumber($var)
{
    return (!empty($var) && is_numeric($var));
}

/**
 * Returns a given default if given value is null
 *
 * @param mixed val
 * @param mixed default
 * @return mixed val or default
 */
function defaultVal($val, $default = null)
{
    // Experiment to see if we can easily remove the strict standards
    // errors with a small function
    if (isset($default) && is_null($default)) {
        return $val;
    } elseif (is_null($val)) {
        return $default;
    }

    return $val;
}

/**
 * cope with nulls or mis-types being uncountable arrays,
 * eg. "$thisMightBeArray = null; $size = count(countableArrayObject($thisMightBeArray));"
 *
 * @param string $tentative
 */
function countableArrayObject($tentative)
{
    return is_array($tentative) ? $tentative : [];
}

/**
 * Turns a title into a slug for meaningful urls,
 * eg. "This is my long Title" => "this-is-my-long-title"
 *
 * @param string $title
 */
function toSlug($title)
{
    return strtolower(str_replace([' ', '_', ',', '.', '/'], '-', $title));
}

/**
 * arranges an array in pages of equal size
 *
 * (1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11)
 *
 * with size 3:
 *
 * ((1,2,3),(4,5,6),(7,8,9),(10,11))
 */
function paginate(Array $array, $pageSize)
{
    return array_chunk($array, $pageSize);
}

/**
 * takes an array of the form
 *
 * ( 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11)
 *
 * and arranges it to
 *
 * ((1,2,3,4,5,6),(7,8,9,10,11))
 *
 * if $noOfColumns was 2
 *
 * always tries to have columns of equal length
 * but the last column can be shorter
 */
function columnize(Array $array, $noOfColumns)
{
    return array_chunk($array, sizeof($array) / $noOfColumns);
}

/**
 *
 * Function to rotate an image if GD is *not* compiled into PHP.
 * This is from beau@dragonflydevelopment.com from the comments at:
 *
 * http://www.php.net/manual/en/function.imagerotate.php
 *
 * @param $img
 * @param $rotation (90, 180, 270, 0, 360)
 */
function rotateImage($img, $rotation)
{
    $width = imagesx($img);
    $height = imagesy($img);
    switch ($rotation) {
        case 90:
        case 270:
            $newimg = @imagecreatetruecolor($height, $width);
            break;
        case 180:
            $newimg = @imagecreatetruecolor($width, $height);
            break;
        case 0:
            return $img;
        case 360:
            return $img;
    }
    if ($newimg) {
        for ($i = 0; $i < $width; $i++) {
            for ($j = 0; $j < $height; $j++) {
                $reference = imagecolorat($img, $i, $j);
                switch ($rotation) {
                    case 90:
                        if (!@imagesetpixel($newimg, ($height - 1) - $j, $i, $reference)) {
                            return false;
                        }
                        break;
                    case 180:
                        if (!@imagesetpixel($newimg, $width - $i, ($height - 1) - $j, $reference)) {
                            return false;
                        }
                        break;
                    case 270:
                        if (!@imagesetpixel($newimg, $j, $width - $i, $reference)) {
                            return false;
                        }
                        break;
                }
            }
        }
        return $newimg;
    }
    return false;
}

/**
 * DEPRECATED from v3.0.0 - use LookupService::lookupForSelect instead
 *
 * Returns a lookup by type from the database and formats
 * it ready for a Html::select field
 *
 * @param Web $w
 * @param string type
 * @return array lookup for select
 */
function lookupForSelect($w, $type)
{
    $select = [];
    $rows = $w->db->get("lookup")->where("type", $type)->fetch_all();
    if ($rows) {
        foreach ($rows ?? [] as $row) {
            $select[] = [$row['title'], $row['code']];
        }
    }
    return $select;
}

/**
 * DEPRECATED from v3.0.0 - define this whenever needed in your module
 *
 * Returns a list of states of Australia structured for a Html::select field
 *
 * @return Array states
 */
function getStateSelectArray()
{
    return array(
        array("ACT", "ACT"),
        array("NSW", "NSW"),
        array("NT", "NT"),
        array("QLD", "QLD"),
        array("SA", "SA"),
        array("TAS", "TAS"),
        array("VIC", "VIC"),
        array("WA", "WA"));
}

/**
 * Iterates over $needle_array and applies $func to $haystack and $current_needle.
 *
 * @param string $haystack
 * @param array $needles
 * @return bool true if item from $needle_array is found in haystack
 */
function strcontains($haystack, $needle_array)
{
    foreach ($needle_array as $needle) {
        if (stripos($haystack, $needle) !== false) {
            return true;
        }
    }

    return false;
}

/**
 * Checks for either a scalar prefix ($needle) in the $haystack
 * or checks whether any of an array of needles is in the $haystack.
 *
 * Returns true when a needle is found at the start of the haystack, false otherwise.
 *
 * @param string $haystack
 * @param string|array $needle
 * @return boolean
 */
function startsWith($haystack, $needle)
{
    if (empty($haystack) || empty($needle)) {
        return false;
    }

    if (is_scalar($needle)) {
        return strpos($haystack, $needle) === 0;
    } elseif (is_array($needle) && sizeof($needle) > 0) {
        foreach ($needle as $pref) {
            if (strpos($haystack, $pref) === 0) {
                return true;
            }
        }
    }
    return false;
}

function str_whitelist($dirty_data, $limit = 0)
{
    if ($limit > 0) {
        $dirty_data = substr($dirty_data, 0, $limit);
    }
    $dirty_array = str_split($dirty_data);
    $clean_data = "";
    foreach ($dirty_array as $char) {
        $clean_char = preg_replace("/[^a-zA-Z0-9 ,.'\"-\/]/", "", $char);
        $clean_data = $clean_data . $clean_char;
    }
    return $clean_data;
}

function phone_whitelist($dirty_data)
{
    $dirty_array = str_split($dirty_data);
    $clean_data = "";
    foreach ($dirty_array as $char) {
        $clean_char = preg_replace("/[^0-9 ()+-]/", "", $char);
        $clean_data = $clean_data . $clean_char;
    }
    return $clean_data;
}

function int_whitelist($dirty_data, $limit)
{
    $dirty_data = substr($dirty_data, 0, $limit);
    $dirty_array = str_split($dirty_data);
    $clean_data = "";
    foreach ($dirty_array as $char) {
        $clean_char = preg_replace("/[^0-9]/", "", $char);
        $clean_data = $clean_data . $clean_char;
    }
    return $clean_data;
}

function getTimeSelect($start = 8, $end = 19)
{
    for ($i = $start; $i <= $end; $i++) {
        $m = " am";
        $t = $i;
        if ($i >= 12) {
            $m = " pm";
            if ($i > 12) {
                $t = $i - 12;
            }
        }
        $t = sprintf("%02d", $t);
        $select[] = array($t . ":00" . $m, $i . ":00");
        $select[] = array($t . ":30" . $m, $i . ":30");
    }
    return $select;
}

/**
 * Formats a date in the given format
 * Can take either unix timestamp or string date
 *
 * @param mixed $date
 * @param string format (optional, default "d/m/Y")
 * @return string|false
 */
function formatDate($date, $format = "d/m/Y")
{
    if (!$date) {
        return null;
    }

    if (!is_long($date) && !is_numeric($date)) {
        $date = strtotime(str_replace("/", "-", $date));
    }

    return date($format, $date);
}

/**
 * Formats a date and time in the given format
 * Can take either unix timestamp or string date
 *
 * @param mixed $date
 * @param string format (optional, default "d/m/Y h:i a")
 * @return string|false
 */
function formatDateTime($date, $format = "d/m/Y h:i a")
{
    return formatDate($date, $format);
}

/**
 * Formats a time in the given format
 * Can take either unix timestamp or string time
 *
 * @param mixed $date
 * @param string format (optional, default "d/m/Y h:i a")
 * @return string|false
 */
function formatTime($date, $format = "H:i")
{
    return formatDate($date, $format);
}

function formatNumber($number)
{
    return sprintf('%.2f', $number);
}

/**
 * A replacement function for the money_format PHP function that is only
 * available on most Linux based systems with the strfmon C function.
 *
 * For those that do not (Windows), then the function is imitated with code.
 *
 * @param string format
 * @param float|mixed number
 */
function formatMoney($format, $number)
{
    if (function_exists('money_format')) {
        setlocale(LC_MONETARY, 'en_AU');
        return money_format($format, doubleval($number));
    }

    $regex = '/%((?:[\^!\-]|\+|\(|\=.)*)([0-9]+)?' .
        '(?:#([0-9]+))?(?:\.([0-9]+))?([in%])/';
    if (setlocale(LC_MONETARY, 0) == 'C') {
        setlocale(LC_MONETARY, '');
    }

    $locale = localeconv();
    if (empty($locale['mon_decimal_point'])) {
        $locale['mon_decimal_point'] = ".";
    }

    if (empty($locale['mon_thousands_sep'])) {
        $locale['mon_thousands_sep'] = ",";
    }

    preg_match_all($regex, $format, $matches, PREG_SET_ORDER);

    foreach ($matches ?? [] as $fmatch) {
        $value = floatval($number);
        $flags = [
            'fillchar' => preg_match('/\=(.)/', $fmatch[1], $fmatch) ? $fmatch[1] : ' ',
            'nogroup' => preg_match('/\^/', $fmatch[1]) > 0,
            'usesignal' => preg_match('/\+|\(/', $fmatch[1], $fmatch) ? $fmatch[0] : '+',
            'nosimbol' => preg_match('/\!/', $fmatch[1]) > 0,
            'isleft' => preg_match('/\-/', $fmatch[1]) > 0,
        ];

        $width = trim($fmatch[2]) ? intval($fmatch[2]) : 0;
        $left = trim($fmatch[3]) ? intval($fmatch[3]) : 0;
        $right = trim($fmatch[4]) ? intval($fmatch[4]) : $locale['int_frac_digits'];
        $conversion = $fmatch[5];

        $positive = true;
        if ($value < 0) {
            $positive = false;
            $value *= -1;
        }

        $letter = $positive ? 'p' : 'n';

        $prefix = $suffix = $cprefix = $csuffix = $signal = '';

        $signal = $positive ? $locale['positive_sign'] : $locale['negative_sign'];
        switch (true) {
            case $locale["{$letter}_sign_posn"] == 1 && $flags['usesignal'] == '+':
                $prefix = $signal;
                break;
            case $locale["{$letter}_sign_posn"] == 2 && $flags['usesignal'] == '+':
                $suffix = $signal;
                break;
            case $locale["{$letter}_sign_posn"] == 3 && $flags['usesignal'] == '+':
                $cprefix = $signal;
                break;
            case $locale["{$letter}_sign_posn"] == 4 && $flags['usesignal'] == '+':
                $csuffix = $signal;
                break;
            case $flags['usesignal'] == '(':
            case $locale["{$letter}_sign_posn"] == 0:
                $prefix = '(';
                $suffix = ')';
                break;
        }

        if (!$flags['nosimbol']) {
            $currency = $cprefix .
                ($conversion == 'i' ? $locale['int_curr_symbol'] : $locale['currency_symbol']) .
                $csuffix;
        } else {
            $currency = '';
        }

        $space = $locale["{$letter}_sep_by_space"] ? ' ' : '';
        $value = number_format($value, $right, $locale['mon_decimal_point'], $flags['nogroup'] ? '' : $locale['mon_thousands_sep']);
        $value = @explode($locale['mon_decimal_point'], $value);

        $n = strlen($prefix) + strlen($currency) + strlen($value[0]);
        if ($left > 0 && $left > $n) {
            $value[0] = str_repeat($flags['fillchar'], $left - $n) . $value[0];
        }

        if (!empty($value)) {
            $value = implode($locale['mon_decimal_point'], $value);
        }

        if ($locale["{$letter}_cs_precedes"]) {
            $value = $prefix . $currency . $space . $value . $suffix;
        } else {
            $value = $prefix . $value . $space . $currency . $suffix;
        }

        if ($width > 0) {
            $value = str_pad($value, $width, $flags['fillchar'], $flags['isleft'] ?
                STR_PAD_RIGHT : STR_PAD_LEFT);
        }

        $format = str_replace($fmatch[0], $value, $format);
    }
    return trim($format);
}

/**
 * Recursively searches though given haystack for a given needle
 *
 * @param array haystack
 * @param mixed needle
 * @param mixed optional array key to only look at when searching (optional)
 * @return mixed key of found needle
 */
function recursiveArraySearch($haystack, $needle, $index = null)
{
    $aIt = new RecursiveArrayIterator($haystack);
    $it = new RecursiveIteratorIterator($aIt);

    while ($it->valid()) {
        if (((isset($index) && ($it->key() == $index)) || (!isset($index))) && ($it->current() == $needle)) {
            return $aIt->key();
        }

        $it->next();
    }

    return false;
}

/**
 * This function will return the correct dates using date picker or a custom month picker.
 * If you have a from date and to date criteria, this function changes the to date based on
 * the dm_var. parse in 'm' for month selection and 'd' for day selection.
 * With date pickers, they always return the beginning of that day. i.e. 00:00:00 10/11/2010
 * this will change the to date to: 23:59:59 10/11/2010.
 *
 * To use this function, use the list function, calling this function.
 * i.e. list($from_date, $to_date) = returncorrectdates($w,$dm_var,$from_date,$to_date);
 *
 * @TODO: Move this somewhere else, we shouldn't be passing Web into functions - maybe even remove?
 *
 * @param Web $w
 * @param string $dm_var
 * @param string $from_date
 * @param string $to_date
 */
function returncorrectdates(Web $w, $dm_var, $from_date, $to_date)
{
    if ($dm_var == 'm') {
        $from_date = strtotime(str_replace("/", "-", $from_date));
        $to_date = strtotime(str_replace("/", "-", $to_date));
        $accepted_date = getDate($to_date);
        $month_number = $accepted_date['mon'];
        $accepted_year = $accepted_date['year'];

        if (date('I')) {
            $minus_var = "3601";
        } else {
            $minus_var = '1';
        }

        if ($to_date) {
            $to_date = $to_date + ((60 * 60 * 24 * cal_days_in_month(CAL_GREGORIAN, $month_number, $accepted_year)) - $minus_var);
        }

        return array($from_date, $to_date);
    }

    if ($dm_var == 'd') {
        $from_date = strtotime(str_replace("/", "-", $_GET['from_date']));
        $to_date = strtotime(str_replace("/", "-", $_GET['to_date']));
        $accepted_date = getDate($to_date);
        $month_number = $accepted_date['mon'];
        $accepted_year = $accepted_date['year'];
        if ($to_date) {
            $to_date = $to_date + 86399;
        }

        return array($from_date, $to_date);
    }
}

/**
 * Find a value in a multidimension array
 * NOTE: This function uses strict type comparison, with one exception where
 * a string $value will match it's integer equivalent (i.e. '1' == 1, but '1s' != 1)
 *
 * Setting the value to an integer will match against non-associative array keys of
 * the same value
 *
 * @param mixed $value
 * @param mixed $array
 * @return bool $in_multiarray
 */
function in_multiarray($value, $array)
{
    if (is_array($array)) {
        if (in_array($value, $array, true) || array_key_exists($value, $array)) {
            return true;
        } else {
            foreach ($array as $key => $arr_value) {
                if (is_array($arr_value) && in_multiarray($value, $arr_value)) {
                    return true;
                }
            }
        }
    } elseif ($value === $array) {
        return true;
    }

    return false;
}

/**
 * Returns a value in a multidimension array
 * NOTE: This function uses strict type comparison, with one exception where
 * a string $value will match it's integer equivalent (i.e. '1' == 1, but '1s' != 1)
 *
 * Similar to above except it will return the value
 *
 * @param mixed $value
 * @param mixed $array
 * @return bool $in_multiarray
 */
function getValueFromMultiarray($key, $array)
{
    if (is_array($array)) {
        if (array_key_exists($key, $array)) {
            return $array[$key];
        } else {
            foreach ($array as $_key => $arr_key) {
                $value = getValueFromMultiarray($key, $arr_key);
                if ($value !== null) {
                    return $value;
                }
            }
        }
    }
    return null;
}

/**
 * This function finds instances of the given $object and returns whether or not
 * a matching instances property has a certain value when stored in a multi
 * dimensional array (value matching only)
 *
 * @param string $object
 * @param string $property
 * @param mixed $value
 * @param array $multiarray
 * @return boolean
 */
function objectPropertyHasValueInMultiArray($object, $property, $value, $multiarray = [])
{
    if (!empty($multiarray)) {
        foreach ($multiarray as $array_key => $array_value) {
            if (is_array($array_value)) {
                return objectPropertyHasValueInMultiArray($object, $property, $value, $array_value);
            }

            if (is_object($array_value) && is_a($array_value, $object, true) && property_exists($array_value, $property) && $array_value->$property === $value) {
                return true;
            }
        }
    }

    return false;
}

/**
 * Find a value in a multidimensional array will only look at the array keys
 *
 * @param mixed value
 * @param mixed array
 * @param int levels (optional, default 3)
 * @return bool
 */
function in_modified_multiarray($value, $array, $levels = 3)
{
    if (is_array($array)) {
        if (in_array($value, $array)) {
            return true;
        } else {
            if (--$levels < 0) {
                return false;
            }

            foreach ($array as $key => $arr_value) {
                if ($value === $key) {
                    return true;
                }

                if (is_array($arr_value) && in_modified_multiarray($value, $arr_value, $levels)) {
                    return true;
                }
            }
        }
    } else {
        if ($value === $array) {
            return true;
        }
    }
    return false;
}

/**
 * DEPRECATED from v3.0.0 - use SSLEncrypt instead
 *
 * Returns AES encrypted string - will now use new SSLEncrypt function
 *
 * @param mixed text to encrypt
 * @param mixed password (unused)
 * @return string encrypted text
 */
function AESencrypt($text, $password)
{
    $this->w->Log->info('AES and override password are deprecated, attempting compatibility through SSL.');

    return SSLencrypt($text);
}

/**
 * DEPRECATED from v3.0.0 - use SSLDecrypt instead
 *
 * Decrypts AES string - will now use new SSLDecrypt function
 *
 * @param mixed text to decrypt
 * @param mixed password (unused)
 * @return string decrypted text
 */
function AESdecrypt($text, $password)
{
    $this->w->Log->info('AES and override password are deprecated, attempting compatibility through SSL.');

    return SSLdecrypt($text);
}

/**
 * Encrypts given text with AES256
 * Warning: this is a two way encryption method, use only if you understand the risks
 *
 * @param mixed text to encrypt
 * @return string encrypted text
 * @throws Exception when system encryption key is missing or IV cannot be generated
 */
function SSLEncrypt($text)
{
    $ssl_method = "AES-256-CBC";
    $encryption_key = Config::get('system.encryption.key', null);
    $encryption_iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($ssl_method));
    
    if (empty($encryption_key) || empty($encryption_iv)) {
        $err = 'Cannot encrypt without system key and IV.';
        $this->w->Log->error($err);
        throw new Exception($err);
    } else {
        $ssl = openssl_encrypt($text, $ssl_method, $encryption_key, 0, $encryption_iv);
        $encryption_iv = bin2hex($encryption_iv);
        return $ssl . "::" . $encryption_iv;
    }
}

/**
 * Decrypts given text with AES256
 *
 * @param mixed text to decrypt
 * @return string decrypted text
 * @throws Exception when system encryption key is missing or IV cannot be generated
 */
function SSLDecrypt($text)
{
    $ssl_method = "AES-256-CBC";
    $encryption_key = Config::get('system.encryption.key', null);
    $text = explode("::", $text);
    $encryption_iv = array_pop($text);

    if (empty($encryption_key) || empty($encryption_iv)) {
        $err = 'Cannot decrypt without system key and IV.';
        $this->w->Log->error($err);
        throw new Exception($err);
    } else {
        $text = array_pop($text);
        return openssl_decrypt($text, $ssl_method, $encryption_key, 0, hex2bin($encryption_iv));
    }
}

/**
 * Gets content between two different strings
 * (Source: http://tonyspiro.com/using-php-to-get-a-string-between-two-strings/)
 *
 * @param string $content
 * @param string $start
 * @param string $end
 * @return string
 */
function getBetween($content, $start, $end)
{
    $r = explode($start, $content);
    if (isset($r[1])) {
        $r = explode($end, $r[1]);
        return $r[0];
    }
    return '';
}

/**
 * Returns true if array is associative, i.e. at least one key index is a string type
 * http://stackoverflow.com/questions/173400/how-to-check-if-php-array-is-associative-or-sequential/4254008#4254008
 *
 * @param array $array
 * @return bool
 */
function is_associative_array($array)
{
    return (bool) count(array_filter(array_keys($array), 'is_string'));
}

/**
 * Similar to above, except it checks that ALL keys are strings
 * @param array $array
 * @return bool
 */
function is_complete_associative_array($array)
{
    return (bool) (count(array_filter(array_keys($array), 'is_string')) == count($array));
}

/**
 * Returns whether or not a given number ($subject) is within the bounds
 * $min and $max. $include is for whether or not to include $min and $max
 * in boundary.
 *
 * I.e. If $min = 1, $max = 10 and $subject is 10:
 *      $include == true will return true (1 <= 10 <= 10 is true)
 *      $include == false will return false (1 < 10 < 10 is false)
 *
 * @param float $subject
 * @param float $min
 * @param float $max
 * @param bool $include
 * @return bool
 */
function in_numeric_range($subject, $min, $max, $include = true)
{
    // Sanity checks
    if (!is_numeric($subject) || !is_numeric($min) || !is_numeric($max)) {
        return false;
    }

    // Check if bounds given in wrong order
    // Has effect of checking outside the boundary
    if ($max < $min) {
        if (true === $include) {
            return ($subject <= $min || $subject >= $max);
        } else {
            return ($subject < $min || $subject > $max);
        }
    }
    // For cases where for some reason all given vars are the same
    if ($min === $max && $min === $subject) {
        return $include;
    }

    // Check
    if (true === $include) {
        return ($subject >= $min && $subject <= $max);
    } else {
        return $subject > $min && $subject < $max;
    }
}

/**
 * Class casting
 * From: http://stackoverflow.com/questions/2226103/how-to-cast-objects-in-php
 *
 * @param string|object $destination
 * @param object $sourceObject
 * @return object
 */
function cast($destination, $sourceObject)
{
    if (is_string($destination)) {
        $destination = new $destination();
    }

    $sourceReflection = new ReflectionObject($sourceObject);
    $destinationReflection = new ReflectionObject($destination);
    $sourceProperties = $sourceReflection->getProperties();
    
    foreach ($sourceProperties ?? [] as $sourceProperty) {
        $sourceProperty->setAccessible(true);
        $name = $sourceProperty->getName();
        $value = $sourceProperty->getValue($sourceObject);
        
        if ($destinationReflection->hasProperty($name)) {
            $propDest = $destinationReflection->getProperty($name);
            $propDest->setAccessible(true);
            $propDest->setValue($destination, $value);
        } else {
            $destination->$name = $value;
        }
    }

    return $destination;
}

/**
 * Deletes string between $beginning and $end inclusive from $string
 *
 * From: http://stackoverflow.com/questions/13031250/php-function-to-delete-all-between-certain-characters-in-string
 *
 * Adapted to do multiple passes over the same string to remove more than once
 * instance of $beginning and $end.
 *
 * @param string $beginning
 * @param string $end
 * @param string $string
 * @return string
 */
function delete_all_between($beginning, $end, $string, $remove_every_instance = false)
{
    $beginningPos = strpos($string, $beginning);
    $endPos = strpos($string, $end);

    if ($beginningPos === false || $endPos === false) {
        return $string;
    }

    if (!$remove_every_instance) {
        return trim(str_replace(substr($string, $beginningPos, ($endPos + strlen($end)) - $beginningPos), '', $string));
    } else {
        while (($beginningPos !== false && $endPos !== false)) {
            $string = trim(str_replace(substr($string, $beginningPos, ($endPos + strlen($end)) - $beginningPos), '', $string));
            $beginningPos = strpos($string, $beginning);
            $endPos = strpos($string, $end);
        }

        return $string;
    }
}

/**
 * Returns all months between two dates in the specified format
 *
 * Thanks to this SO answer: https://stackoverflow.com/a/18743012
 *
 * @param string from date in the format d-m-Y
 * @param string to date in the format d-m-Y
 * @param string optional format of returned values
 * @return array<string> month list
 */
function get_list_of_months_between_dates($from, $to, $format = 'M Y')
{
    if (is_numeric($from)) {
        $from = date('d-m-Y', $from);
    }

    if (is_numeric($to)) {
        $to = date('d-m-Y', $to);
    }

    $start = (new DateTime($from))->modify('first day of this month');
    $end = (new DateTime($to))->modify('first day of next month');
    $interval = DateInterval::createFromDateString('1 month');
    $period = new DatePeriod($start, $interval, $end);

    $month_list = [];
    foreach ($period as $dt) {
        $month_list[] = $dt->format($format);
    }

    return $month_list;
}
