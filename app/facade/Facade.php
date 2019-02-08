<?php
/**
 * 为类设置短名称
 * (暂时只规划处这一个作用)
 */

namespace app\facade;

class Facade
{
    public static function bootstrap(){
        self::setAlias();
    }

    public static function setAlias()
    {
        // 支持classAlias方式,直接在这里设置短名称
        class_alias('play\log\BaseLogger', 'Log');
        class_alias('play\di\Container', 'Container');
    }
}