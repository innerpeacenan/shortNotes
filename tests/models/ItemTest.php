<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 6/7/17
 * Time: 8:37 PM
 */
namespace tests;

use n\models\Items;
use tests\lib\ArrayDataSets;

/**
 * Description: description
 * @runTestsInSeparateProcesses
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
     *Call to undefined method PHPUnit_Extensions_Database_DB_DefaultDatabaseConnection::query()
     *
     */
    public function setUp()
    {
        // 得先运行父类方法,以便准备好运行的基镜(texture)
        parent::setUp();
        // 设置该表的字增主键从6开始
        $this->stub->query("ALTER TABLE `items` AUTO_INCREMENT = 6;");
        /**
         * 匿名类,阅后即焚, php7 新特性
         * @var @anonymous  $temp
         *
         */
        $this->items = new  Items ();
        // 特别注意,各个方法的数据库数据虽然会每次都清空,但是全局变量,类的静态变量等不会被清空,创建的对象也不会销毁,只是彼此间不再统一作用域名里边罢了
//        var_dump($this->items->getPrimaryKey());
    }

   public function tearDown()
   {
       parent::tearDown();
       if (isset($this->items)){
//           $this->items->setPrimaryKey('id');
       }
   }

    /**
     * @access
     * @return Items
     * @runInSeparateProcess
     */
    public function testInsert()
    {
        //       测试插入能否正常工作
        $item = $this->items;
//        说明字增主键设置成功了
//        $item->id = 6;
        $item->name = 'test insert';
//        测试魔术 set 方法是否工作正常
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
        $this->assertTrue($inserted);
        /*
         * 测试是否插入成功,也有可能后下面这个load方法有问题
         */
        $item->setPrimaryKey(['id', 'user_id']);
        $item->load(['id' => 6, 'user_id' => 1]);
//        $item->setPrimaryKey(['id']);
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
        return $item;
    }

    /**
     *  注意单元测试的方法之间,数据库和数据之前都是完全隔离的,这也是单元测试迷人的地方,
     * 但是有时候之前的测试意外中止,其数据库并没有来得及清空,可能对下一个测试方法产生影响,
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

// UPDATE `items` SET `fid` = 1 WHERE `id`=1 AND `user_id`=1
    public function testUpdate()
    {
        $items = $this->items;
        $this->assertEquals(['id'], $items->getPrimaryKey());
        $items->load(1);
        $this->assertNotNull($items);
        $items->fid = 1;
        $items->t_left = 5;
        $items->name = "phpUnit";
        $this->assertTrue($items->update());
        // 重新从数据库加载
        $items->load(1);
        $this->assertEquals(1, $items->fid);
        $this->assertEquals("phpUnit", $items->name);
        $this->assertTrue($items->delete());
    }


}