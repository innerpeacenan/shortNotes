<?php

namespace play\di;
/**
 * 全局类，方便快速操作
 *
 */
class G
{
    /**
     * @var \play\web\Application
     */
    public static $app;
    /**
     * 全局的对象注册容器
     * @var \play\di\Container
     */
    private static $container;

    public function setContainer(Container $container)
    {
        self::$container = $container;
    }

    /**
     * @param string | array $config
     * $config must contain a 'class' element
     * @return object
     * @throws \Exception unsupported configuration type or an array without a 'class' member
     */
    public static function createObject($config)
    {
        if (!isset(self::$container)) {
            self::$container = new Container();
        }

        if (is_string($config)) {
            // 取单例模式
            return static::$container->get($config);
        } elseif (is_array($config) && isset($config['class'])) {
            // 每次创建一个新的对象
            return static::$container->get($config);
        } else {
            throw new \Exception('Unsupported configuration type: ' . gettype($config));
        }
    }

}
