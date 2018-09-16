<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 6/24/17
 * Time: 11:38 AM
 */

namespace nxn;

/**
 * Class StringHelper
 * @package nxn
 * User: xiaoning nan
 * Date: 2017-06-{24}
 * Time: xx:xx
 * Description: description
 */
class Str
{
    /**
     * @access
     * @param $str
     * @return string
     *
     *
     * Description:
     *
     * 功能同 php 内置函数 strrev
     * strrev("Hello world!"); // outputs "!dlrow olleH"*
     */
    public static function strrev($str)
    {
        $len = strlen($str) - 1;
        $new = '';
        for ($i = $len; $i >= 0; $i--) {
            $new .= $str[$i];
        }
        return $new;
    }

    public static function lower($value)
    {
        return mb_strtolower($value, 'UTF-8');
    }

    public static function snakeCase($value, $delimiter = '_')
    {
        if (!ctype_lower($value)) {
            $value = preg_replace('/\s+/u', '', ucwords($value));
            $value = static::lower(preg_replace('/(.)(?=[A-Z])/u', '$1' . $delimiter, $value));
        }
        return $value;
    }

    /**
     * Determine if a given string ends with a given substring.
     *
     * @param  string $haystack
     * @param  string|array $needles
     * @return bool
     */
    public static function endsWith($haystack, $needles)
    {
        foreach ((array)$needles as $needle) {
            if (substr($haystack, -strlen($needle)) === (string)$needle) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if a given string starts with a given substring.
     *
     * @param  string $haystack
     * @param  string|array $needles
     * @return bool
     */
    public static function startsWith($haystack, $needles)
    {
        foreach ((array)$needles as $needle) {
            if ($needle !== '' && substr($haystack, 0, strlen($needle)) === (string)$needle) {
                return true;
            }
        }

        return false;
    }


    /**
     * Returns the portion of string specified by the start and length parameters.
     *
     * @param  string $string
     * @param  int $start
     * @param  int|null $length
     * @return string
     */
    public static function substr($string, $start, $length = null)
    {
        return mb_substr($string, $start, $length, 'UTF-8');
    }
}