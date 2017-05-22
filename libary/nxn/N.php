<?php

/**
 * @ 全局类，方便快速操作
 *
 */
class N
{
    public static $app;
    /**
     * 全局的对象注册容器
     * @var \nxn\web\di\Container
     */
    public static $container;

    /**
     *
     * @param string | array $config
     * $config must contain a 'class' element
     * @return object
     * @throws \Exception unsupported configuration type or an array without a 'class' member
     */
    public static function createObject($config)
    {
        if (is_string($config)) {
            // 取单例
            return static::$container->get($config);
        } elseif (is_array($config) && isset($config['class'])) {
            $class = $config['class'];

            // bug: /home/wwwroot/www.note.com/libary/nxn/N.php
            // 用 get 方法是非常错误的
            if(empty($config['params'])) $config['params'] = [];
            if(empty($config['config'])) $config['config'] = [];
            return static::$container->get($class, $config['params'], $config['config']);
        } else {
            throw new \Exception('Unsupported configuration type: ' . gettype($config));
        }
    }
}

N::$container = new nxn\web\di\Container();