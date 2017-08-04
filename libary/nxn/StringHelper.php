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
class StringHelper
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
    public function strrev($str)
    {
        $len = strlen($str) - 1;
        $new = '';
        for ($i = $len; $i >= 0; $i--) {
            $new .= $str[$i];
        }
        return $new;
    }

}