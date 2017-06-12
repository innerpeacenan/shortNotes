<?php
namespace tests;

use PHPUnit\Framework\TestCase;
use n\models\Items;

class ItemsTest extends TestCase
{
    /**
     * @var Items
     */
    public $items;

    public function setUp()
    {
        /**
         * 匿名类,阅后即焚, php7 新特性
         * @var @anonymous  $temp
         *
         */
        $this->items = new class extends Items
        {
            /**
             * @param string | array $primaryKey
             * @return void
             *
             */
            public static function setPrimaryKey($primaryKey)
            {
                if (is_string($primaryKey)) {
                    $primaryKey = [$primaryKey];
                } elseif (!is_array($primaryKey)) {
                    throw new \Exception('primary key must be an array or string!');
                }
                // 采用匿名类以后, 这里不能再用 static 或者 self 关键字了,因为类里边的 self 指的是外边的这个类
                Items::$_primaryKey = $primaryKey;
            }
        };
        parent::setUp();
    }

    /*
     * 当给了不存在的表名称的时候,为什么 PDO 并没有抛出异常,
       当给了不正常的表明的时候,phpunit 会触发 Segmentation fault (core dumped) 这个c语言级别的错误
     * @todo this it really confusing
     */
    public function testLoad()
    {
        $item = $this->items;
//        $item->load(6);
        $item->setPrimaryKey(['id', 'user_id']);
        $item->load(['id' => 6, 'user_id' => 1]);
        return $item;
    }

    /**
     * @param  $item
     * @depends testLoad
     *
     *
     */
    public function testOldAttribute(Items $item)
    {
        $oldAttr = array(
            'id' => 6,
            'fid' => 0,
            'depth' => 0,
            't_left' => 0,
            't_right' => 0,
            'user_id' => 1,
            'name' => 'test',
            'rank' => 0,
            'c_time' => '2017-05-07 13:42:28',
            'u_time' => '2017-05-07 13:42:28',
            'status' => 'enable',
        );
        $this->assertEquals($oldAttr, $item->getOldAttribute());
    }


    public function anonymousFun()
    {
        $this->items->setBehavior(
            new class
            {
                public function log($msg)
                {
                    echo $msg;
                }
            }
        );
    }
// UPDATE `items` SET  WHERE `id`=6
    //Failed asserting that false matches expected 1.
    public function testUpdate()
    {
        $item = $this->items;
        $item->load(6);
        $item->name = "test";
        $this->assertEquals(1, $item->update(false));
    }

}