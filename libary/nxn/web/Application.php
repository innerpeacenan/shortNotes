<?php

namespace nxn\web;

use nxn\di\Container;

/**
 * @property \nxn\web\RouterInterface router
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
     * @var null| \nxn\web\Controller | \nxn\web\AuthController $controller is a string before instantiate
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

    /**
     * if you what to run other action, even cross controller, just reset router and call [[ runAction() ]]
     * @param  array | null $router
     * @return void
     * @throws \Exception
     * 当不够 action 部分的路径的时候,默认导向哥他请求的主页看
     */
    public function setRouter($router = null)
    {
        if (isset($router)) {
            $this->router = $router;
        }
    }


    /**
     * 通过 [[setRouter]] 方法, 保证 controller 和 action 都是正确的
     * @access
     * @return string
     */
    public function runAction()
    {
        $this->setRouter($this->router);
    }

    public function run()
    {
        try {
            $this->runAction();
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            $code = $e->getCode();
            $header = [];
            Ajax::json($code, [], $msg, $code);
        } catch (\Throwable $err) {
            $msg = $err->getMessage();
            $code = $err->getCode();
            if (empty($code)) {
                $code = 500;
            }
            Ajax::json($code, [], $msg, $code);
        }

    }


    /**
     * @access
     * @return mixed
     * Created by: xiaoning nan
     * Last Modify: xiaoning nan
     *
     *
     *
     * Description:
     *
     * 连接前先打乱数据库配置粗数数组,保障连接的随机性
     *
     * request params:
     *
     *
     */
    public function getMasterDb()
    {
        if (null === $this->getSingleton('master')) {
            $pool = $this->conf['db']['master'];
            shuffle($pool);
            $config = reset($pool);
            $config['params'][] = $this->conf['db']['shareParam'];
            $master = \N::createObject($config);
            $this->setSingletons('master', $master);
        }
        return $this->getSingleton('master');
    }


    /**
     * @access
     * @return mixed
     *
     * Description:
     *
     *
     *
     */
    public function getSlaveDb()
    {
        if (null === $this->getSingleton('slave')) {
            if (!isset($this->conf['db']['slave'])) {
                return $this->getMasterDb();
            }
            $pool = $this->conf['db']['slave'];
            shuffle($pool);
            $config = reset($pool);
            $config['params'][] = $this->conf['db']['shareParam'];
            $slave = \N::createObject($config);
            $this->setSingletons('slave', $slave);
        }
        return $this->getSingleton('slave');
    }


}
