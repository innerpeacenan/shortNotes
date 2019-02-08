<?php

namespace play\web;

interface RouterInterface
{

    public function getController();


    public function getAction();

    /**
     * @param $controller
     */
    public function setController(string $controller);

    /**
     * @param $action string
     */
    public function setAction(string $action);

    /**
     * 运行controller的特定action
     * @param array $params
     * @return mixed
     *
     */
    public function run($params = []);

}