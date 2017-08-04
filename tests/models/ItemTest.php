<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 6/7/17
 * Time: 8:37 PM
 */
namespace tests;

use n\models\Items;
use nxn\web\Application;
use \PDO;

/**
 * @runInSeparateProcess
 */
class ItemTest extends ArrayDataSets
{
    /**
     * @var Items $items
     */
    public $items;

    public function dataSet()
    {
        // 之前给的数据格式不对   array_replace(): Argument #2 is not an array
        $arr = [
            'items' => [
                [
                    'id' => 1,
                    'fid' => 0,
                    'depth' => 0,
                    't_left' => 0,
                    't_right' => 0,
                    'user_id' => 1,
                    'name' => 'test insert',
                    'rank' => 0,
                    'c_time' => '2017-05-07 05:42:28',
                    'u_time' => '2017-05-07 05:42:28',
                    'status' => 'enable',
                ],
            ],
            'notes' => [

            ]
        ];
        return $arr;
    }

    /**
     * 除了 setUp(), 其他的 protected  类型的方法都不会被在测试的时候直接调用
     */
    public function setUp()
    {
        // 得先运行父类方法,以便准备好运行的基镜(texture)
        parent::setUp();
        // 设置该表的字增主键从6开始
        $this->pdo->query("ALTER TABLE `items` AUTO_INCREMENT = 6;");
        $config = [
            'db' => [
                'class' => 'PDO',
                'params' => [
                    'mysql:host=localhost;dbname=notes_test',
                    'root',
                    1111111,
                    [
                        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
                        PDO::ATTR_PERSISTENT => true,
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    ]
                ]
            ],
        ];
        \N::$app = new Application($config);
        $this->items = new  Items ();
        // 特别注意,各个方法的数据库数据虽然会每次都清空,但是全局变量,类的静态变量等不会被清空,创建的对象也不会销毁,只是彼此间不再统一作用域名里边罢了
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    public function testInsert()
    {
        $item = $this->items;
//      说明字增主键设置成功了
        $item->name = 'test insert';
//      测试魔术 set 方法是否工作正常
        $this->assertArrayHasKey('name', $item->getAttribute());
        $item->user_id = 1;
        $item->fid = 0;
        $item->depth = 0;
        $item->t_left = 0;
        $item->t_right = 0;
        $item->rank = 0;
        $item->c_time = "2017-05-07 05:42:28";
        $item->u_time = "2017-05-07 05:42:28";
        $item->status = "enable";
        $inserted = $item->insert(false);
        $this->assertEquals(1, $inserted);
        $this->assertEquals(6, $item->id);
        /*
         * 测试是否插入成功,也有可能后下面这个load方法有问题
         */
        $item->setPrimaryKey(['id', 'user_id']);
        $expected = [
            'id' => 6,
            'fid' => 0,
            'depth' => 0,
            't_left' => 0,
            't_right' => 0,
            'user_id' => 1,
            'name' => 'test insert',
            'rank' => 0,
            'c_time' => '2017-05-07 05:42:28',
            'u_time' => '2017-05-07 05:42:28',
            'status' => 'enable',
        ];
        $this->assertEquals($expected, $item->getOldAttribute());
        $this->assertEquals($expected, $item->getAttribute());
        $item = $item->load(['id' => 6, 'user_id' => 1]);
        $expected = [
            'id' => 6,
            'fid' => 0,
            'depth' => 0,
            't_left' => 0,
            't_right' => 0,
            'user_id' => 1,
            'name' => 'test insert',
            'rank' => 0,
            'c_time' => '2017-05-07 05:42:28',
            'u_time' => '2017-05-07 05:42:28',
            'status' => 'enable',
        ];
        $this->assertEquals($expected, $item->getOldAttribute());
        $this->assertEquals($expected, $item->getAttribute());
        // 测试数据类型是否相等
        $this->assertTrue(6 === $item->id);
    }

    /**
     *  注意单元测试的方法之间,数据库和数据之前都是完全隔离的,这也是单元测试迷人的地方,
     *  但是有时候之前的测试意外中止,其数据库并没有来得及清空,可能对下一个测试方法产生影响,
     *  测试魔术方法对不存在的字段抛出异常,并且异常信息情况
     * @expectedException \Exception
     * @expectedExceptionMessage column :ct_time does not exists in table items
     */
    public function testMagicSet()
    {
        if (isset($this->items)) {
            $this->items->ct_time = "2017-05-07 05:52:28";
        }
    }

// UPDATE `items` SET `fid` = 1 WHERE `id`=1 , `user_id`=1
    public function testUpdate()
    {
        $items = $this->items;
        $this->assertEquals(['id'], $items->getPrimaryKey());
        $items = Items::load(1);
        $this->assertNotNull($items);
        $items->fid = 1;
        $items->t_left = 5;
        $items->name = "phpUnit";
        $this->assertTrue(1 === $items->update());
        // 重新从数据库加载
        $items = Items::load(1);
        $this->assertEquals(1, $items->fid);
        $this->assertEquals("phpUnit", $items->name);
        // Failed asserting that 1 is true.
//      $this->assertTrue($items->delete());
        $this->assertTrue(1 === $items->delete());
    }
}