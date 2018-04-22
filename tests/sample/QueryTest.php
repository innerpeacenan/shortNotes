<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 6/7/17
 * Time: 8:37 PM
 */
namespace tests;

use nxn\web\Application;
use \PDO;

/**
 * @runInSeparateProcess
 */
class QueryTest extends ArrayDataSets
{
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
                // POD 构造函数的最后一个参数从这里传入
                'shareParam' => [
                    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_PERSISTENT => true
                ],
                'master' => [
                    [
                        'class' => 'PDO',
                        'params' => [
                            'mysql:host=localhost;dbname=notes',
                            'root',
                            1111111,
                        ]
                    ],
                ],
                'slave' => [
                    [
                        'class' => 'PDO',
                        'params' => [
                            'mysql:host=localhost;dbname=notes',
                            'root',
                            1111111,
                        ],
                    ]
                ]
            ],
        ];
	\N::$app = new Application($config);
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    public function testOne(){

    }

    public function testAll(){

    }

    public function testExecute(){

    }
}
