<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 6/10/17
 * Time: 12:55 PM
 */
namespace tests\modules\index\Note;

use n\models\Notes;
use tests\Controller;

/**
 * @runTestsInSeparateProcesses
 * Description: description
 * Indicates that all Tests in a test class should be run in a separate PHP process.
 */
class DeleteNotePostTest extends Controller
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
    }

    public function testDeleteNote()
    {
        // 这个数据在测试数据桩里边
        $ar = (new Notes())->load(112);
        $this->assertNull($ar);
    }

    /**
     * @expectedException \PHPUnit_Framework_Error_Notice
     * @expectedExceptionMessage  Undefined index: id
     * 测试各个测试组建能否较好的彼此隔离
     * 这个测试案例写在这里其实不太合适,主要是为了测试 run test in separate process 是否工作正常
     */
    public function testOk()
    {
        $_SERVER['id'];
    }
}