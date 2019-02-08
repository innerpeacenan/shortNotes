<?php

namespace test\sample;

use play\debug\VarDumper;
use PHPUnit\Framework\TestCase;

class VarDump extends TestCase
{

    public function testDumpAsString()
    {
        $output = <<<STR
<code><span style="color: #000000">
<span style="color: #0000BB"></span>
</span>
</code>
STR;
        VarDumper::dumpAsString(5);
        $this->assertNotContains('&lt;?php', VarDumper::$output, true);
    }

    public function testDumpInternal()
    {

        VarDumper::dumpInternal(5, 0);
        $this->assertEquals('5', VarDumper::$output);
        VarDumper::$output = "";
        // test array
        // hash table 的特性: duplicate array key
        $var = ['test', 'hash' => 'table', 'subarray' => [0 => 2, '0' => "2", true, null, '2h']];
        $this->assertEquals(4, count($var['subarray']));
//        设置默认深度,不然会有问题
        VarDumper::$depth = 10;
        VarDumper::dumpInternal($var, 0);
        $str = <<<STR
[
    0 => 'test',
    'hash' => 'table',
    'subarray' => [
            0 => '2',
            1 => true,
            2 => null,
            3 => '2h',
        ],
]
STR;
        $this->assertEquals($str, VarDumper::$output, 'two based array');

        // 接下来测试对象的输出格式

//        todo
    }

    /**
     * @backupStaticAttributes enabled
     */
    public function testVarDumpThreeBaseArray()
    {
        VarDumper::$depth = 10;
        VarDumper::$output = "";
        $var = ['test', 'hash' => '', 'subarray' => [0 => 2, [0, 2, 3], true, null, '2h']];
        $str = <<<STR
[
    0 => 'test',
    'hash' => '',
    'subarray' => [
            0 => 2,
            1 => [
                    0 => 0,
                    1 => 2,
                    2 => 3,
                ],
            2 => true,
            3 => null,
            4 => '2h',
        ],
]
STR;
        VarDumper::dumpInternal($var, 0);
        $this->assertEquals($str, VarDumper::$output);;
    }


    public function testArrayKeysWithZero()
    {
        VarDumper::$depth = 10;
        VarDumper::$output = "";
        $var = ['test', 'hash' => '', 'subarray' => [0 => 2, [0, 2, 3], true, null, '2h']];
        $str = <<<STR
[
    0 => 'test',
    'hash' => '',
    'subarray' => [
            0 => 2,
            1 => [
                    0 => 0,
                    1 => 2,
                    2 => 3,
                ],
            2 => true,
            3 => null,
            4 => '2h',
        ],
]
STR;
        VarDumper::dumpInternal($var, 0);
        $this->assertEquals($str, VarDumper::$output);;

    }

    /**
     * @access
     * todo private and protect property of a class should be tested later
     */
    public function testObject()
    {
        VarDumper::$depth = 10;
        VarDumper::$output = '';
        $pro1 = new \stdClass();
        $object = new \stdClass;
        $object->foo = 1;
        $object->bar = 2;
        $object->pfirst = $pro1;
        $object->psecond = $pro1;
        get_object_vars($object);
        VarDumper::dumpInternal($object, 0);
//      这一块需要进一步优化,弄明白具体的处理方式
//    第二次准备遍历对象的时候,由于之前的数据已经缓存了结果,所以不许要完成打印对象的信息了,只需要输出类名,编号和括号: stdClass#2(...)
        $this->assertEquals("stdClass#1
(
    [foo] => 1
    [bar] => 2
    [pfirst] => stdClass#2
    (
    )
    [psecond] => stdClass#2(...)
)", VarDumper::$output);
    }
}
