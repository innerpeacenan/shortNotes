<?php

namespace play\Event;

/**
 * Interface DispatcherInterface
 * 确定生产者和消费者模式最少需要的接口
 */
interface DispatcherInterface
{
    /**
     * 注册观开始观察
     * params $event string
     * params $listener callback
     */

    public function addListener($event, $listener, $payload);

    public function getListeners($event);

    public function dispatch($event);
}