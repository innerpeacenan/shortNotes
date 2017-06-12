<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 6/10/17
 * Time: 12:55 PM
 */
namespace tests\modules\index\Note;

use n\models\Items;
use n\models\Notes;
use tests\Controller;

/**
 * @runTestsInSeparateProcesses
 * Description: description
 * Indicates that all tests in a test class should be run in a separate PHP process.
 */
class MoveNotePostTest extends Controller
{
    public function dataSet()
    {
        return [
            'items' => [
                [
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
                ],
            ],
            'notes' => [
                [
                    'id' => 112,
                    'item_id' => 65,
                    'content' => 'ok"ljkkfa"<htmld> <?php ?>',
                    'c_time' => '2017-06-10 12:28:02',
                    'status' => 1,
                ],
            ],
        ];
    }

    public function setRequest()
    {
        $_REQUEST['id'] = 112;
        $_REQUEST['itemId'] = 64;
    }

    public function testMoveNote()
    {
        // 这个数据在测试数据桩里边
        $item_id = t('type_of_item_id');
        $this->assertEquals('integer',gettype($item_id));
        $this->assertTrue(true === t('status'));
        $note = (new Notes())->load(112);
        $this->assertEquals(64,$note->item_id);
    }
}