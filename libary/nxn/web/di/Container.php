<?php

namespace nxn\web\di;

use ReflectionClass;

/**
 * 这是依赖注入的容器,有关依赖注入的相关内容, 我回头写一个详细的贴子(目前这一版本的依赖注入容器比较简单)
 * ```
 *
 * ```
 * @package nxn\web\di
 *
 */
class Container
{
    /**
     * 存放单例
     * @var array
     */
    private $_singletons;

    /**
     * 设置单例对象
     * @param string $class
     * @param object $instance
     * @return void
     */
    public function setSigleton($class, $instance)
    {
        $this->_singletons[$class] = $instance;
    }

    /**
     * 目前的方案为: 容器只缓存单例模式的实例对象,对其他类型,现场分析
     * @access
     * @param $class
     * @return  object an instance of the requested class
     *
     */
    public function get($class, $param = [], $config = [])
    {
        if (isset($this->_singletons[$class])) {
            // singleton
            return $this->_singletons[$class];
        } else {
            return $this->build($class, $param, $config);
        }
    }

    /**
     * 根据类名称实例化一个对象
     * 自动绑定（Autowiring）自动解析（Automatic Resolution）
     * [ 根据 param 将这个类进一步细化,以达到更好的效果 ]
     * @param \closure | string $class
     * @param array $params 构造函数需要使用的参数
     * @param array $config 配置参数(在对象实例化后,对对象相应的属性赋值)
     * @return object
     * @throws \Exception
     */
    public function build($class, $params = [], $config = [])
    {
        $reflection = new ReflectionClass($class);
        // 检查类是否可实例化, 排除抽象类abstract和对象接口interface
        if (!$reflection->isInstantiable()) {
            throw new \Exception('class: ' . $class . ' is not instantiable. Check if it is a abtract class.');
        }
        // 递归解析构造函数的参数
        $constructor = $reflection->getConstructor();
        // 若无构造函数，直接实例化并返回
        if ($constructor === null) $object = new $class;
        // 取构造函数参数,通过 ReflectionParameter 数组返回参数列表
        $parameters = $constructor->getParameters();
        //根据构造函数的参数解决依赖
        $dependence = $this->getDependencies($parameters);
        // 用配置重新更改类的构造参数值
        // 传之前检查是否是数组
        foreach ($params as $key => $param) {
            $dependence[$key] = $param;
        }
        // 解析一个类所依赖的所有参数
        $object = $reflection->newInstanceArgs($dependence);
        // 根据配置文件,给对应属性复制
        foreach ($config as $member => $value) {
            $config->$member = $value;
        }
        return $object;
    }

    /**
     * 根据构造器的参数数组,取得该类的所有依赖.具体方法为: 对每一个参数,通过 ReflectionParameter::getClass() 方法,
     * 得到该参数对应的 ReflectionClass , 如果 reflectionClass === null, 说明该参数不是一个类, 则检查该参数是否有默认的值,
     * 对于参数是一个对象的情况,在调用 Container::build 方法新建实例对象
     *
     *  Returns the dependencies of the specified class.
     * @param array $params
     * @return array
     * @throws \Exception
     */
    public function getDependencies($params)
    {
        $dependencies = [];
        /**
         * @var \ReflectionParameter $parameter
         */
        foreach ($params as $parameter) {
            /**
             * @var \ReflectionClass $dependency
             **/
            $dependency = $parameter->getClass();

            if ($dependency !== null) {
                if ($parameter->isDefaultValueAvailable()) {
                    // 是变量,有默认值则设置默认值
                    // 有默认值则返回默认值
                    $dependencies[] = $parameter->getDefaultValue();
                } else {
                    // 说明是一个类，递归解析
                    // resovle dependence
                    $dependencies[] = $this->build($dependency->name);
                }
            }
        }
        return $dependencies;
    }
}