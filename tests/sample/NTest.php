<?php

namespace test\sample;

use PHPUnit\Framework\TestCase;

/**
 * Class ApplicationTest
 * @runInSeparateProcess
 */
class NTest extends TestCase
{

    public function setUp()
    {
        parent::setUp();
    }

    public function testCreateObject()
    {
        $config = [
            'class' => 'PDO',
            'params' => [
                0 => 'mysql:host=192.168.31.205;dbname=notes_11',
                1 => 'root', 2 => 1111111,
                3 => [
                    1002 => 'SET NAMES utf8',
                    3 => 2,
                    12 => true,
                ],
            ],
        ];
    }
}
