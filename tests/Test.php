<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 6/23/17
 * Time: 1:37 PM
 */
namespace tests;

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
    /**
     * @access
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
}