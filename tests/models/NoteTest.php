<?php

namespace tests;

use n\models\Notes;
use PHPUnit\Framework\TestCase;

class NoteTest extends TestCase
{
    /**
     * @var Notes
     */
    public $notes;

    public function setUp()
    {
        $this->notes = new Notes();
        parent::setUp();
    }


    public function columnsProvider()
    {
        return [
            ['id',
                [
                    'type' => 'integer',
                    'allowNull' => true,
                    'default' => null,
                    'Comment' => ''
                ]
            ],
            ['item_id',
                [
                    'allowNull' => true,
                    'default' => NULL,
                    'Comment' => 'eq to items.id, 项目ID',
                    'type' => 'integer',
                ],
            ],
            [
                'content',
                [
                    'allowNull' => true,
                    'default' => NULL,
                    'Comment' => '',
                    'type' => 'string'
                ]
            ],
            [
                'c_time',
                [
                    'allowNull' => true,
                    'default' => 'CURRENT_TIMESTAMP',
                    'Comment' => '创建时间',
                    'type' => 'string',
                ]
            ],
            [
                'status',
                [
                    'allowNull' => false,
                    'default' => '1',
                    'Comment' => '',
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
