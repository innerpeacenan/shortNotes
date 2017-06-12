<?php

namespace tests;

use n\models\Items;
use PHPUnit\Framework\TestCase;

/**
 * Class ItemTest
 * @package tests
 * User: xiaoning nan
 * Date: 2017-05-{30}
 * Time: xx:xx
 * Description: description
 */
class ItemsTest extends TestCase
{

    public function testARShowFullColumns()
    {
        $item = new Items();
        // 为什么单元测试的结果和单步走的结果不一样呢?这实在是让人困惑
        $this->assertFalse($item->tableSchema());
    }

}