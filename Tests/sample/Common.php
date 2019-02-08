<?php
namespace Tests\sample;

use Tests\Test;

/**
 * Class Common
 * @package Tests\sample
 * User: xiaoning nan
 * Date: 2017-06-{23}
 * Time: xx:xx
 * Description: description
 */
class Common extends Test
{
    public function testArray_slice()
    {
        $arr = ["first", "second", "third"];
        $result = array_slice($arr, 0, -1);
        $this->assertEquals(array(
            0 => 'first',
            1 => 'second',
        ), $result);
    }

    /**
     * [数组默认是按属性值传递](http://www.cnblogs.com/lovebing/p/6063849.html)
     *
     */
    public function testCallByReference()
    {
        $hero = array('no1' => '蝙蝠侠', 'no2' => '超人');
        $hero2 = $hero;
        $hero2['no1'] = '蜘蛛侠';
	// 第一个数组并未跟着改动
        $this->assertEquals('蝙蝠侠', $hero['no1']);
        $hero2 = &$hero;
        $hero2['no1'] = '蜘蛛侠';
        $this->assertEquals('蜘蛛侠', $hero['no1']);

    }

    public function testObjectToArray()
    {
        function object_array($array)
        {
            if (is_object($array)) {
                $array = (array)$array;
            }
            if (is_array($array)) {
                foreach ($array as $key => $value) {
                    $array[$key] = object_array($value);
                }
            }
            return $array;
        }

        $obj = new class
        {
            public $p1 = ['hello', 'world'];
            public $p2 = ['hello', 'world'];
        };

        $result = [
            'p1' => [
                0 => 'hello',
                1 => 'world',
            ],
            'p2' => [
                0 => 'hello',
                1 => 'world',
            ],
        ];
        $converted = object_array($obj);
        $this->assertEquals($result, $converted);
    }


    /**
     * @access
     * @return void
     * Created by: xiaoning nan
     * Last Modify: xiaoning nan
     *
     *
     *
     * Description:
     *
     * [进制转化](http://blog.csdn.net/xiaofei2010/article/details/7434737)
     *
     * // 整数除以2，商继续除以2，得到0为止，将余数逆序排列
     *
     * //小数乘以2，取整，小数部分继续乘以2，取整，得到小数部分0为止，将整数顺序排列, 达到最大存储范围停止
     *
     * @todo 动手实现 version compare 函数
     */
    public function testNum()
    {

        if (version_compare('5.6.0', PHP_VERSION, '>')) {
            fwrite(
                STDERR,
                'This version of PHPUnit requires PHP 5.6; using the latest version of PHP is highly recommend'
            );
        }
    }

    /**
     * @access
     * @return void
     * Created by: xiaoning nan
     * Last Modify: xiaoning nan
     *
     *
     *
     * Description:
     * stream_resolve_include_path() 可以获取一个文件名称的绝对路径
     * request params:
     * name |meaning
     * ---|---
     * get_defined_vars 获取当前作用与的所有变量,局部的和全局的都包括
     *
     *
     */
    public function testStreamResolveIncludePath()
    {
        $fullPath = stream_resolve_include_path(__FILE__);
	$this->assertEquals(__DIR__ . '/Common.php', $fullPath);
    }


    /**
     * @test
     */
    public function trash(){
        defined('APP_BASE_PATH') or define('APP_BASE_PATH', __DIR__ . '../');
        var_dump(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR);
    }


}


