<?php

declare(strict_types=1);

namespace App\Helpers;

/**
 * DateTime Helper for formatting dates and times.
 */
class DateTimeHelper
{
    /**
     * Format a date/time string using a specified format.
     *
     * @param string $datetime The date/time string to format.
     * @param string $format The format string (default: 'Y-m-d H:i:s').
     * @return string The formatted date/time.
     */
    public static function format(string $datetime, string $format = 'Y-m-d H:i:s'): string
    {
        try {
            $dt = new \DateTime($datetime);
            return $dt->format($format);
        } catch (\Exception $e) {
            // Return original if invalid
            return $datetime;
        }
    }

    /**
     * Format to a human-readable date (e.g., 'December 13, 2025').
     *
     * @param string $datetime The date/time string.
     * @return string The formatted date.
     */
    public static function formatHumanDate(string $datetime): string
    {
        return self::format($datetime, 'F j, Y');
    }

    /**
     * Format to a human-readable date and time (e.g., 'December 13, 2025 14:30:00').
     *
     * @param string $datetime The date/time string.
     * @return string The formatted date and time.
     */
    public static function formatHumanDateTime(string $datetime): string
    {
        return self::format($datetime, 'F j, Y H:i:s');
    }

    /**
     * Format to ISO 8601 (e.g., '2025-12-13T14:30:00+00:00').
     *
     * @param string $datetime The date/time string.
     * @return string The formatted ISO date/time.
     */
    public static function formatISO(string $datetime): string
    {
        return self::format($datetime, \DateTime::ISO8601);
    }

    /**
     * Get the current date in Y-m-d format.
     *
     * @return string The current date.
     */
    public static function today(): string
    {
        return date('Y-m-d');
    }

    /**
     * Get the current date and time in Y-m-d H:i:s format.
     *
     * @return string The current date and time.
     */
    public static function now(): string
    {
        return date('Y-m-d H:i:s');
    }
}