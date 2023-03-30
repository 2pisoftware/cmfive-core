<?php

class DateFormatter
{
    const FORMAT_DATE_TIME = 'Y-m-d H:i:s';
    const FORMAT_DATE = 'Y-m-d';
    const FORMAT_TIME = 'H:i:s';

    // public static function toDateTimeString(string $input, string $format)
    // {
    //     return (new DateTime($input ?? 'now'))->format($format);
    // }

    public static function stringToUnix(string $input)
    {
        return (new DateTime(str_replace("/", "-", $input)))->getTimestamp();
    }
    /**
     * Formats a timestamp
     * per default MySQL datetime format is used
     *
     * @param $time
     * @param $format
     */
    public static function unixToDateTimeString(int $time = null, string $format = self::FORMAT_DATE_TIME)
    {
        return (new DateTime())->setTimestamp($time)->format($format);
        // return formatDate($time ? $time : time(), $format, false);
    }

    /**
     * Formats a timestamp
     * per default MySQL date format is used
     *
     * @param $time
     * @param $format
     */
    public static function unixToDate(int $time = null, string $format = self::FORMAT_DATE)
    {
        return self::unixToDateTimeString($time, $format);
        // return formatDate($time ? $time : time(), $format, false);
    }

    public static function unixToTime(int $time = null, string $format = self::FORMAT_TIME)
    {
        return self::unixToDateTimeString($time, $format);
        // return date($format, $time ? $time : time());
    }

    public static function dateTimeToUnix(string $date_time)
    {
        return self::stringToUnix($date_time);
        // return strtotime(str_replace("/", "-", $dt));
    }

    public static function dateToUnix(string $date)
    {
        return self::stringToUnix($date);
        // return strtotime(str_replace("/", "-", $d));
    }

    public static function timeToUnix(string $time)
    {
        return self::stringToUnix($time);
        // return strtotime(str_replace("/", "-", $t));
    }
}