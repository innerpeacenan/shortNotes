<?php

namespace play;

use app\facade\Facade;
use play\di\Container;
use play\log\BaseLogger;

//只是个壳子

class Application
{

    protected $bootstrapped = false;

    /**
     * @var array
     */
    public $bootstrapper = [
        BaseLogger::class,
        // get env
        Env::class,
        // get config
        Config::class,
        // register facades
        Facade::class,
        // should bootstrap providers
        Container::class,
    ];

    // 设置错误级别,时区等一系列基础设置
    protected function init()
    {
        date_default_timezone_set("Asia/Shanghai");
    }

    public function __construct()
    {
        if (false === $this->bootstrapped) {
            $this->init();
            $this->bootstrap();
            $this->bootstrapped = true;
        }
    }

    public function bootstrap()
    {
        foreach ($this->bootstrapper as $bootstrapper) {
            try{
                (new $bootstrapper)->bootstrap();
            }catch (\Throwable $e){
                throw new \Exception('bootstrapper:' . $bootstrapper . 'failed' . $e->getMessage());
            }
        }
    }

    public function make(string $str)
    {
        return Container::get($str);
    }
}
