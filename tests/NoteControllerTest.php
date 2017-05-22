<?php
/*
 * This file is part of the Money package.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace tests;

use n\modules\index\controllers\IndexController;
use PHPUnit\Framework\TestCase;

class NoteControllerTest extends TestCase
{

    public function additionProvider()
    {
        return [
            ['hello', false],
            ['index', false],
            ['indexAction', true],
        ];
    }

    /**
     * @dataProvider additionProvider
     *
     *
     */
    public function testA($test,$result)
    {
        $controller = new IndexController();
        $this->assertEquals(method_exists($controller,$test),$result);
        $stack = [];
        $this->assertEquals(0, count($stack));

    }


}
