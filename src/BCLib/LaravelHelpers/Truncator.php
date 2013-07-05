<?php

namespace BCLib\LaravelHelpers;

/**
 * Class Truncator
 *
 * Truncate text.
 *
 * @package BCLib\LaravelHelpers
 */
class Truncator
{
    public static function truncate($string, $num_chars)
    {
        $suffix = '';
        $response = explode("\n", wordwrap($string, $num_chars));
        if (sizeof($response) > 1) {
            $suffix = '...';
        }
        return $response[0] . $suffix;
    }
}