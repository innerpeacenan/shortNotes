<?php
/**
 * Created by PhpStorm.
 * User: nanxiaoning
 * Date: 1/26/2019
 * Time: 11:31 PM
 */

namespace play\web;


use play\di\Container;

class Kernel
{
    public $router;

    public function __construct()
    {

    }

    // deal with input val, for web kernel,its http request
    public function capture()
    {

    }


    public function handle()
    {
        $this->run();
    }

    /**
     * if you what to run other action, even cross controller, just reset router and call [[ runAction() ]]
     * @param  array | null $router
     * @return void
     * @throws \Exception
     * 当不够 action 部分的路径的时候,默认导向哥他请求的主页看
     */
    protected function setRouter(RouterInterface $router = null)
    {
        if (isset($router)) {
            $this->router = $router;
        }
    }

    protected function run()
    {
        try {
            $this->router = Container::get('router');
            $this->setRouter($this->router);
        } catch (\Throwable $err) {
            $code = $err->getCode();
            if (empty($code)) {
                $code = 500;
            }
            Ajax::json($code, $err->getTrace(), $err->getMessage(), $code);
        }
    }
}