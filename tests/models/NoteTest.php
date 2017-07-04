<?php

namespace tests;

use n\models\Notes;
use PHPUnit\Framework\TestCase;
use nxn\web\Application;
use \PDO;

class NoteTest extends TestCase
{
    /**
     * @var Notes
     */
    public $notes;

    public function setUp()
    {        // active record 依赖 application
        $config = [
//    PHPUnit_Framework_Exception: PHP Fatal error:  Uncaught PDOException: You cannot serialize or unserialize PDO instances in -:336
// 当以多进程的方式跨库操作的时候,会报上面的错误
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
//  prerequisites list: Application
        \N::$app = new Application($config);
//      需要将 config 配置过来
        \N::$app->conf = $config;
        // 新建表的时候会建立表模式
        $this->notes = new Notes();
        parent::setUp();
    }


    public function columnsProvider()
    {
        return [
            ['id',
                [
                    'type' => 'integer',
                    'allowNull' =>false,
                    'default' => null,
                    'comment' => ''
                ]
            ],
            ['item_id',
                [
                    'allowNull' =>false,
                    'default' => NULL,
                    'comment' => 'eq to items.id, 项目ID',
                    'type' => 'integer',
                ],
            ],
            [
                'content',
                [
                    'allowNull' =>false,
                    'default' => NULL,
                    'comment' => '',
                    'type' => 'string'
                ]
            ],
            [
                'c_time',
                [
                    'allowNull' =>false,
                    'default' => 'CURRENT_TIMESTAMP',
                    'comment' => '创建时间',
                    'type' => 'string',
                ]
            ],
            [
                'status',
                [
                    'allowNull' =>true,
                    'default' => '1',
                    'comment' => '',
                    'type' => 'integer',
                ]
            ]
        ];
    }

    public function testTableName()
    {
        $this->assertEquals('notes', $this->notes->tableName());
        $this->assertEquals('notes', Notes::tableName());
    }

    /**
     * @dataProvider columnsProvider
     */
    public function testColumns($name, $expect)
    {
        $colums = $this->notes->columns;
        $this->assertEquals($expect, $colums[$name]);
    }

    public function testPrimaryKey()
    {
        // Array (...) does not match expected type "string".
//      注意, primaryKey 是数组
//      [0 => 'id',1 => 'id'] 弄清为什么会会重复
        $this->assertEquals([0 => 'id'], Notes::getPrimaryKey());
    }


}
