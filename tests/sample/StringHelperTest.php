<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 6/24/17
 * Time: 11:39 AM
 */
namespace tests\sample;

use nxn\StringHelper;
use tests\Test;

class StringHelperTest extends Test
{
    /**
     * @var StringHelper
     */
    public $stub;

    public function setUp()
    {
        parent::setUp();
        $this->stub = new StringHelper();
    }

    public function testStrrev()
    {
        // php strrev() will convert logical new line to physical new line
        $this->assertEquals('!dlrow
olleH', $this->stub->strrev("Hello\nworld!"));
    }


}