<?php

namespace Pandao\Common\Utils;

/**
 * Class DateUtils
 * - strftime
 * - gmStrToTime
 * - gmTime
 */

class DateUtils
{
    /**
     * Formats a timestamp according to a given format.
     *
     * @param string $format The format string.
     * @param int|null $timestamp The Unix timestamp (optional).
     * @param bool $utc Whether to use UTC for the time zone (optional).
     * @return string The formatted date/time string.
     */
    public static function strftime($format, $timestamp = null, $utc = false)
    {
        $timestamp = $timestamp ?? time();
        $date = new \DateTime();
        $date->setTimestamp($timestamp);
        if ($utc) $date->setTimezone(new \DateTimeZone('UTC'));

        $formatMap = [
            '%a' => 'D', '%A' => 'l', '%b' => 'M', '%B' => 'F', '%c' => 'c',
            '%C' => 'Y', '%d' => 'd', '%D' => 'm/d/y', '%e' => 'j', '%F' => 'Y-m-d',
            '%g' => 'y', '%G' => 'o', '%h' => 'M', '%H' => 'H', '%I' => 'h',
            '%j' => 'z', '%m' => 'm', '%M' => 'i', '%n' => "\n", '%p' => 'A',
            '%P' => 'a', '%r' => 'h:i:s A', '%R' => 'H:i', '%S' => 's', '%t' => "\t",
            '%T' => 'H:i:s', '%u' => 'N', '%U' => 'W', '%V' => 'W', '%w' => 'w',
            '%x' => 'x', '%X' => 'X', '%y' => 'y', '%Y' => 'Y', '%z' => 'O',
            '%Z' => 'T', '%%' => '%'
        ];

        foreach ($formatMap as $strftimeFormat => $dateTimeFormat) {
            $format = str_replace($strftimeFormat, $dateTimeFormat, $format);
        }

        return $date->format($format);
    }

    /**
     * Converts a date string to a Unix timestamp, using GMT time.
     *
     * @param string $date The date string.
     * @return int The Unix timestamp.
     */
    public static function gmStrToTime($date)
    {
        date_default_timezone_set('UTC');
        $time = strtotime($date.' GMT');
        date_default_timezone_set(PMS_TIME_ZONE);
        return $time;
    }

    /**
     * Returns the current Unix timestamp, adjusted to GMT.
     *
     * @return int The current Unix timestamp in GMT.
     */
    public static function gmTime()
    {
        date_default_timezone_set('UTC');
        $time = time();
        date_default_timezone_set(PMS_TIME_ZONE);
        return $time;
    }
}
