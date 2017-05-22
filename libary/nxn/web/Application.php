<?php
namespace nxn\web;

use nxn\web\di\Container;


/**
 * @todo 目前还很不健全,需要进一步优化, 但是我还没有想好怎么做
 * @package nxn\web
 * User: xiaoning nan
 * Date: 2017-05-{13}
 * Time: xx:xx
 * Description: description
 */
class Application extends Container
{
    public $conf;
    public $container;
    /**
     * [module,controller,action]
     * @var array | null null on default and array consist of module, controller , action when called [[Application::setRouter]]
     */
    public $router = [
        'module' => 'n\\modules\\index\\controllers\\',
        'controller' => 'n\\modules\\index\\controllers\\IndexController',
        'action' => 'indexAction'
    ];

    /**
     * @var null|  object $controller is a string before instantiate
     */
    public $controller;

    public function __construct($config)
    {
        \N::$app = $this;
        $this->conf = $config;
        //@todo @do more
    }

    /**
     * 借助依赖注入容器，实例化对象
     * @param $name
     * @return object
     */
    public function __get($name)
    {
        $this->conf[$name];
        $conf['params'] = isset($conf['params']) ? $conf['params'] : [];
        $conf['config'] = isset($conf['config']) ? $conf['config'] : [];
        return $this->get($conf['class'], $conf['params'], $conf['config']);
    }

    /**
     * if you what to run other action, even cross controller, just reset router and call [[ runAction() ]]
     * @param  array | null $router
     * @return void
     * @throws \Exception
     *
     */
    public function setRouter($router = null)
    {
        if (isset($router)) {
            $this->router = $router;
        }
        //如果从来没有设置过,则从url中解析
        // 从 url 中解析  getfriends?fid=2Action
        // Undefined index: REQUEST_URI
        $baseUrl = explode('?', $_SERVER['REQUEST_URI'])[0];
        //经验教训: explode 之前,先检查 trim 结果是否为空
        // must trim the leading slash in request url. learn more about http
        // 这里主意运算符的优先级别
        if (($rest = trim($baseUrl, '/')) !== '') {
            $r = $this->router;
            $part = explode('/', $rest);
            $method = $_SERVER['REQUEST_METHOD'];
            switch (count($part)) {
                case 0:
                    break;// if no path decleared, use default
                case 1:
                    $r['action'] = lcfirst(static::camelCase($part[0])) . $method;
                    break;
                case 2:
                    $r['controller'] = $r['module'] . static::camelCase($part[0]) . 'Controller';

                    $r['action'] = lcfirst(static::camelCase($part[1])) . $method ;
                    break;
                case 3:
                    // modules is just like a directory, do not need to camel case
                    $r['module'] = 'n\\modules\\' .$part[0] . '\\controllers\\';
                    $r['controller'] = $r['module'] . static::camelCase($part[1]) . 'Controller';
                    $r['action'] = lcfirst(static::camelCase($part[2])) . $method;
                    break;
                default:
                    header('HTTP/1.1 400 BAD REQUEST(invalide reuqest path)', true, 400);
                    exit();
            }
            // 如何防止破坏变量系统呢
            $this->router = $r;
        }
        $controller = $this->router['controller'];
        //@ 修改了 autoload 类，优化了请求的状态返回
        //@todo 需要封装一个简单的 response 类，以更据不同的情况，返回不同的状态码
        if (!class_exists($controller, true)) {
            header('HTTP/1.1 400 BAD REQUEST(invalide controller)', true, 400);
            exit();
        }
        $this->controller = new $controller();
        if (!method_exists($this->controller, $this->router['action'])) {
            header('HTTP/1.1 400 BAD REQUEST(invalide action)', true, 400);
            exit();
//            throw new \Exception('404 bad request! the controller file is:' . $this->getControllerName($this->router));
        }

    }

    /**
     * @access
     * @param $string
     * @return string
     * turn string like 'information-detect' into 'InformationDetect'
     *
     *
     *
     */
    public static function camelCase($string)
    {
        return str_replace('-', '', ucwords($string, '-'));
    }

    /**
     * 通过 [[setRouter]] 方法, 保证 controller 和 action 都是正确的
     * @access
     * @return string
     *
     */
    public function runAction()
    {
        $this->setRouter();
        // 解析 action 的类名称
        return call_user_func([$this->controller, $this->router['action']]);
    }

    public function run()
    {
        $this->container = new Container();
        \N::$app = $this;
        $this->runAction();
    }
}
