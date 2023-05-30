<?php

namespace App\Support\Helpers;

class StringHelper
{
    /**
     * Extracts the string between the start and end delimeters
     *
     * @param  string   $string
     * @param  string   $start
     * @param  string   $end
     * @return string
     */
    public static function getInnerSubstring(
        string $string,
        string $start = "",
        string $end = ""
    ): string {

        return preg_replace("/(.*)$start(.*)$end(.*)/s", '\2', $string);
    }

    /**
     * Extracts all the substrings between the start and end delimeters
     *
     * @param  string  $string
     * @param  string  $start
     * @param  string  $end
     * @return array
     */
    public static function getAllInnerSubstring(
        string $string,
        string $start = "",
        string $end = ""
    ): array{
        $regex = "/$start([^{}]*)$end/";
        return preg_match_all($regex, $string, $matches) ? $matches[1] : [];
    }

    /**
     * Replaces the substring of a string with another substring provided
     *
     * @param  string   $replace
     * @param  string   $replaceWith
     * @param  string   $string
     * @return string
     */
    public static function replaceString(
        string $replace,
        string $replaceWith,
        string $string,
    ): string {
        return str_replace($replace, $replaceWith, $string);
    }

    /**
     * get string between two strings
     *
     * @param  string      $content
     * @param  string      $start
     * @param  string|null $end
     * @return string
     */
    public static function getBetween(
        string $content,
        string $start,
        string | null $end
    ): string {
        $r = explode($start, $content);
        if (isset($r[1])) {
            if ($end) {
                $r = explode($end, $r[1]);
                return trim($r[0]);
            }
            return trim($r[1]);
        }
        return '';
    }
}