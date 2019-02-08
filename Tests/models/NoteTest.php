<?php
namespace Tests;

use n\models\Notes;
use PHPUnit\Framework\TestCase;
use play\web\Application;
use \PDO;

class NoteTest extends TestCase
{
    /**
     * @var Notes
     */
    public $notes;

    public function setUp(){
        $config = [];
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
        $this->assertEquals([0 => 'id'], Notes::getPrimaryKey());
    }


}
