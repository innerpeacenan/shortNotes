<?php


namespace play\Event;
/**
 * Interface DispatcherInterface
 * 确定生产者和消费者模式最少需要的接口
 */
class Dispatcher implements DispatcherInterface
{

    /**
     * @var array
     *  存放所有的观察者 ['event_name' => [callback, payload]]
     */
    protected $listeners = [];

    public function on($event, $listener, $payload)
    {
        if (!is_array($payload)) {
            $payload = [$payload];
        }
        if (is_string($event)) {
            if (is_callable($listener)) {
                $this->listeners[$event][] = [$listener, $payload];
            } else {
                throw new \Exception('other type not supported yet');
            }
        } else {
            throw new \Exception('other type not supported yet');
        }
    }

    /**
     * 注册观开始观察
     * params $event string
     * params $listener callback
     */
    public function addListener($event, $listener, $payload)
    {
        $this->on($event, $listener, $payload);
    }

    public function getListeners($event)
    {
        if (is_string($event)) {
            return isset($this->listeners[$event]) ? $this->listeners[$event] : [];
        } else {
            throw new \Exception('other type not supported yet');
        }

    }

    public function dispatch($event)
    {
        foreach ($this->getListeners($event) as $conf) {
            list($listener, $payload) = $conf;
            // to avoid array to string conversion error, 这也是 call_user_func 和 call_user_func_array 的区别
            call_user_func_array($listener, $payload);
        }
    }
}