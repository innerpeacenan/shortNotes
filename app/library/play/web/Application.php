<?php

namespace play\web;

use play\di\Container;

/**
 * @property \play\web\RouterInterface router
 *
 */
class Application extends Container
{
    public $conf;

    /**
     * @var Container 注入的新建对象的容器
     */
    public $container;


    /**
     * @var null| \play\web\Controller | \play\web\AuthController $controller is a string before instantiate
     */
    public $controller;

    public function __construct($config)
    {
        $this->container = new Container();
        \N::$app = $this;
        $this->conf = $config;
    }

    /**
     * 借助依赖注入容器，实例化对象
     * @param string $name 一个配置的别名
     * @return object
     * @throws \Exception getting non-exists and non-configured property
     */
    public function __get($name)
    {
        if (method_exists($this, 'get' . $name)) {
            $getter = 'get' . $name;
            // $getter  后边跟一对 () 表示调用该方法,而不是调用该属性.这样当 $name = "db' 的时候,getDb()才能够被调用
            return $this->$getter();
        }
        if (isset(\N::$app->conf[$name])) {
            // 直接按照别名取,这样会按照单例模式处理
            return $this->container->get($name);
        }
        throw new \Exception('getting non-exists and non-configured property: ' . $name . ' of object: '
            . get_class($this));
    }







}
