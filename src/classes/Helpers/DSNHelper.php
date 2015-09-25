<?php

namespace DbEasy\Helpers;

/**
 * User: sergeymartyanov
 * Date: 25.09.15
 * Time: 0:32
 */
class DSNHelper
{
    public static function parse($dsn)
    {
        $parsed = parse_url($dsn);
        if (!$parsed) return null;
        $params = null;
        if (!empty($parsed['query'])) {
            parse_str($parsed['query'], $params);
            $parsed += $params;
        }
        $parsed['dsn'] = $dsn;
        return $parsed;
    }

    public static function parseScheme($dsn)
    {
        $parsed = parse_url($dsn, PHP_URL_SCHEME);
        return ($parsed) ? $parsed : null;
    }
}