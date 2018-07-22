<?php

namespace nxn\pipeline;

/*
 * 函数式编程略有不习惯
 */

class PipeLine implements PipeLineInterface
{

    protected $travelor;
    protected $stops;
    protected $method;

    // 旅行才刚刚开始,跟我走吧,天亮就出发
    public function send($travelor)
    {
        $this->travelor = $travelor;
        return $this;
    }

    // 我要从南走到北
    public function through($stops)
    {
        $this->stops = is_array($stops) ? $stops : func_get_args();
        return $this;
    }

    public function via($method)
    {
        $this->method = $method;
    }

    public function then(\Closure $destination)
    {
        // 获得 base pipeline
        $firstSlice = $this->getInitialSlice($destination);

        // 维持一个中间件stack结构, 让后添加的有限执行,从而获得更高的优先级
        $pipes = array_reverse($this->stops);

        return call_user_func(
        // 柯里化
            array_reduce($pipes, $this->getSlice(), $firstSlice), $this->travelor
        );

    }

    protected function getInitialSlice($destination)
    {
        return function ($passable) use ($destination) {
            return call_user_func($destination, $passable);
        };

    }

    protected function getSlice()
    {
        // 第一个参数为剩余栈, 第二个参数为当前处理想项目
        return function ($stack, $pipe) {
            // 在pipeline里边,每次返回值都为一个函数,表示一个动作,一条流水线作业
            return function ($passable) use ($stack, $pipe) {
                if ($pipe instanceof \Closure) {
                    // 处理完成当前流水线
                    return call_user_func($pipe, $passable, $stack);
                }
            };
        };

    }
}