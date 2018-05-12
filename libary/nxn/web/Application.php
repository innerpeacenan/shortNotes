<?php
namespace nxn\web;

use nxn\di\Container;


/**
 * @todo 目前还很不健全,需要进一步优化, 但是我还没有想好怎么做
 * @package nxn\web
 * Description: description
 *
 * @ 重构,重新写一个 component 类用于依赖加载的单例模式的处理
 */
class Application extends Container
{
    public $conf;
    /**
     * @var Container 注入的新建对象的容器
     */
    public $container;
    /**
     * [module,controller,action]
     * @var array  array consist of module, controller , action whill be reswhen called [[Application::setRouter]]
     */
    public $router = [
        'module' => 'n\\modules\\index\\controllers\\',
        'controller' => 'IndexController',
        'action' => 'indexGET'
    ];

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
            return;
        }
        //如果从来没有设置过,则从uri中解析
        // 从 uri 中解析  getfriends?fid=2Action
//       防止有 '/' 这种类型的路径直接访问
        $baseUri = explode('?', $_SERVER['REQUEST_URI'])[0];
        $method = $_SERVER['REQUEST_METHOD'];
        $left = preg_split('/\\//', $baseUri, -1, PREG_GREP_INVERT);
        if (empty($left)) return;
        if (empty($_FILES) && !in_array($method, ['POST', 'GET'])) {
            if (false != ($rawData = file_get_contents('php://input'))) {
                if (false != mb_parse_str($rawData, $data)) {
                    $_REQUEST = $data;
                }
            }
        }
        $this->router['action'] = $method . static::camelCase(array_pop($left));
        if (empty($left)) return;
        $this->router['controller'] = static::camelCase(array_pop($left)) . 'Controller';
        if (empty($left)) return;
        // 到 module 的时候,只做简单的替换,大小写等其他信息均保留
        $this->router['module'] = 'n\\modules\\' . join('\\', $left) . '\\controllers\\';
    }

    /**
     * @access
     * @param $string
     * @return string
     * turn string like 'information-detect' into 'InformationDetect'
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
        $controller = $this->router['module'] . $this->router['controller'];
        if (!class_exists($controller, true)) {
            header('HTTP/1.1 400 BAD REQUEST(invalide controller)', true, 400);
            header('Content-type:text/html;charset=utf-8');
            echo '400 BAD REQUEST(invalide controller)';
            exit();
        }
        $this->controller = new $controller();
        $this->controller->beforeAction();
        if (!method_exists($this->controller, $this->router['action'])) {
            $msg = 'HTTP/1.1 400 BAD REQUEST(invalid action' . json_encode($this->router, 256) . ')';
            header($msg, true, 400);
            echo '400 bad request,invalide action name' . json_encode($this->router['action'],256);
            exit();
        }
        list($baseUri, $queryString) = array_pad(explode('?', $_SERVER['REQUEST_URI'], 2), 2, '0');
        $method = $_SERVER['REQUEST_METHOD'];
        if(strtolower($method) === 'get'){
            $params = $queryString;
        }else{
            $params = json_encode($_REQUEST, JSON_UNESCAPED_UNICODE);
        }
        \Log::request('method<' . $method . 'path<' . $baseUri . '<params<' . $params . '<');
        // 解析 action 的类名称
        return call_user_func([$this->controller, $this->router['action']]);
    }

    public function run()
    {
        try{
            return $this->runAction();
        }catch (\Exception $e){
            $msg = $e->getMessage();
            $code = $e->getCode();
            $header = [];
            Ajax::json($code, [], $msg, $code);
        }catch (\Throwable $err){
            $msg =  $err->getMessage();
            $code = $err->getCode();
            if(empty($code)){
                $code = 500;
            }
            \Log::error($err->getTraceAsString());
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
