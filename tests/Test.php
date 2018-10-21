<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 6/23/17
 * Time: 1:37 PM
 */

namespace tests;

use nxn\Str;
use nxn\web\Application;
use PHPUnit\Framework\TestCase;

/**
 * Class Common
 * @package tests\sample
 * User: xiaoning nan
 * Date: 2017-06-{23}
 * Time: xx:xx
 * Description: description
 */
class Test extends TestCase
{
    public function setup(){
        parent::setup();
    }
    /**
     *
     * @deprecated
     * @param $obj
     * @return  mixed
     * 用于获取对象的
     */
    public function getInvisableProperty($obj, $property)
    {
        $r = new  \ReflectionObject($obj);
        $p = $r->getProperty($property);
        $p->setAccessible(true); // <--- you set the property to public before you read the value
        $reult = $p->getValue($obj);
//      不要影响该类的属性的正常使用
        $p->setAccessible(false);
        return $reult;
    }

    protected function method($obj, $name, $args = [])
    {
        $class = new \ReflectionClass(get_class($obj));
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method->invokeArgs($obj, $args);
    }

    public function __get($name)
    {
        if (Str::endsWith($name, 'Stub')) {
            $name = Str::substr($name, 0, -strlen('Stub'));
        }
        return $this->getProperty($this->stub, $name);
    }

    public function __set($name, $value)
    {
        if (Str::endsWith($name, 'Stub')) {
            $name = Str::substr($name, 0, -strlen('Stub'));
        }
        return $this->setProperty($this->stub, $name, $value);
    }


    public function __call($name, $arguments)
    {
        if (Str::endsWith($name, 'Stub')) {
            $name = Str::substr($name, 0, -strlen('Stub'));
        }
        return $this->method($this->stub, $name, $arguments);
    }

    protected function getProperty($obj, $name)
    {
        $class = new \ReflectionClass(get_class($obj));
        $property = $class->getProperty($name);
        $property->setAccessible(true);
        return $property->getValue($obj);
    }

    protected function setProperty($obj, $name, $value)
    {
        $class = new \ReflectionClass(get_class($obj));
        $property = $class->getProperty($name);
        $property->setAccessible(true);
        $property->setValue($obj, $value);
    }
}