<?php

namespace nxn\web;

use nxn\Exceptions\HttpResponseException;


class MatchRouter implements RouterInterface
{

    public $controller;

    public $action;

    public function __construct()
    {
        //如果从来没有设置过,则从uri中解析
        // 从 uri 中解析  getfriends?fid=2Action

        $baseUri = trim(explode('?', $_SERVER['REQUEST_URI'])[0]);

        $method = $_SERVER['REQUEST_METHOD'];
        $routesMap = require N_APPLICATION . '/config/routes.php';
        $controller = '';
        $action = '';
        
        if (isset($routesMap[$baseUri])) {
            list($controller, $action) = explode('@', $routesMap[$baseUri]);
        } else {
            throw new HttpResponseException('path [' . $baseUri . '] not exist!');
        }


        // 如果没有文件上传, 对put方法,直接从输入流里边获取数据
        if (empty($_FILES) && !in_array($method, ['POST', 'GET'])) {
            if (false != ($rawData = file_get_contents('php://input'))) {
                if (false != mb_parse_str($rawData, $data)) {
                    $_REQUEST = $data;
                }
            }
        }

        $this->setController($controller);
        $this->setAction($method . $action);

        list($baseUri, $queryString) = array_pad(explode('?', $_SERVER['REQUEST_URI'], 2), 2, '0');

        $method = $_SERVER['REQUEST_METHOD'];

        if (strtolower($method) === 'get') {
            $params = $queryString;
        } else {
            $params = json_encode($_REQUEST, JSON_UNESCAPED_UNICODE);
        }
        \Log::request('method[' . $method . ']path[' . $baseUri . ']params[' . $params . ']');

        $this->run($_REQUEST);
    }

    public function setController($controller)
    {
        if (!class_exists($controller, true)) {
            throw new HttpResponseException('HTTP/1.1 400 BAD REQUEST, invalide controller', 400);
        }
        $this->controller = $controller;
    }

    public function setAction($action)
    {
        if (!method_exists($this->getController(), $action)) {
            throw new HttpResponseException('400 bad request,invalide action name', 400);
        }
        $this->action = $action;
    }

    public function getController()
    {
        if (is_string($this->controller)) {
            $this->controller = new $this->controller();
        }
        return $this->controller;
    }

    /**
     * @param $action string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * 运行controller的特定action
     * @param array $params
     * @return void
     *
     */
    public function run($params = [])
    {
        if (method_exists($this->getController(), 'beforeAction')) {
            call_user_func_array([$this->getController(), 'beforeAction'], $params);
        }
        call_user_func_array([$this->getController(), $this->getAction()], $params);
    }
}