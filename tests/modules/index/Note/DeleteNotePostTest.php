<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 6/10/17
 * Time: 12:55 PM
 */
namespace tests\modules\index\NoteController;

use n\models\Notes;
use n\modules\index\controllers\NoteController;
use tests\lib\ArrayDataSets;

/**
 * Class TestNoteController
 * @runTestsInSeparateProcesses
 * @package tests
 * Description: description
 * Indicates that all tests in a test class should be run in a separate PHP process.
 */
class DeleteNotePostTest extends ArrayDataSets
{
    /**
     * @var NoteController
     */
    public $stub;

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


    public function testDelteNote()
    {
        // todo 写一个方法,根据方法名称,还原请求路由,
        //写一个方法,实现action强制有返回值
        $c = preg_split('/,/',get_called_class(),-1,PREG_SPLIT_NO_EMPTY);
//tests\modules\index\NoteController\DeleteNotePostTest
//        [$c[2],preg_split('/(?=[A-Z])/', substr($c[3],0,-10), -1, PREG_SPLIT_NO_EMPTY),$c[4]];
//       2,3,4
        $_REQUEST['id'] = 112;
        $_SERVER['REQUEST_URI'] = '/index/note/delete-note';
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $ini = require(__DIR__ . '/../../../../config/test.php');
        $result = (new \nxn\web\Application($ini))->run();
        $this->assertTrue($result['status']);
//        Failed asserting that an object is empty.
        $ar = (new Notes())->load(112);
        $this->assertNull($ar);
    }

    /**
     * @expectedException \PHPUnit_Framework_Error_Notice
     * @expectedExceptionMessage  Undefined index: REQUEST_URI
     * 测试各个测试组建能否较好的彼此隔离
     */
    public function testOk()
    {
        $_SERVER['REQUEST_URI'];
    }
}