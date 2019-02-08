<?php
namespace app\modules\index\controllers;

use app\models\Image;
use app\models\Notes;
use app\models\Tags;
use play\web\Ajax;
use app\modules\account\controllers\AuthController;


/**
 * Class AjaxController
 * @package n\modules\index
 * User: xiaoning nan
 * date:2017-05-24
 * time:08:24:16
 * Description: description
 */
class NoteController extends AuthController
{

    /**
     * @access
     * @return void
     * Created by: xiaoning nan
     * Last Modify: xiaoning nan
     * Description:
     *
     * request params:
     * name |meaning
     * ---|---
     * item_id | notes.item_id or item.id
     * limit | 每页显示几条
     * gage | 页数
     */
    public function getItemNotes()
    {
        $userId = $_SESSION['user_id'];
        $itemId = $_REQUEST['item_id'];
        $result = Notes::notesByItem($userId, $itemId, $_REQUEST );
        if ($result) {
            Ajax::json(true, $result, 'success');
        } else {
            Ajax::json(0);
        }
    }

    public function getItemBackupNotes(){
        $userId = $_SESSION['user_id'];
        $itemId = $_REQUEST['item_id'];
        $result = Notes::notesByItem($userId, $itemId, $_REQUEST, [Tags::$defaultTags['done']]);
        if ($result) {
            Ajax::json(true, $result, 'success');
        } else {
            Ajax::json(0);
        }
    }

    /**
     * @access
     * @return string
     * Created by: xiaoning nan
     * Last Modify: xiaoning nan
     * Description:
     * 暂且修改为彻底删除
     */
    public function deleteNote()
    {
        $note = Notes::load($_REQUEST['id']);
        if (null === $note) {
            Ajax::json(false, [], 'can not find related note with id:' . $_REQUEST['id']);
        }
        $deleted = $note->delete();
        return Ajax::json($deleted);
    }


    /**
     * @access
     * @return void
     * Created by: xiaoning nan
     * Last Modify: xiaoning nan
     * Description:
     *
     * request params:
     * name |meaning
     * ---|---
     * id: notes.id, 如果没有值, 则执行插入操作
     * item_id: notes.item_id, namely items.id
     * content: notes.content
     */
    public function postNote()
    {
        $message = "";
        $note = new Notes();
        $note->setAttributes($_REQUEST);
        $status = $note->save(false);
        $id = $note->id;
        if(0 === $status){
            $status = 1;
        }
        Ajax::json($status, ['id' => $id], $message);
    }

    /**
     * @access
     * @return void
     * Created by: xiaoning nan
     * Last Modify: xiaoning nan
     */
    public function putMoveNote()
    {
        $note = Notes::load($_REQUEST['id']);
        if ($note === null) {
            Ajax::json(false);
            return;
        }
        $note->scenario = 'update';
        $note->setAttributes($_REQUEST);
        $status = $note->update(false);
        Ajax::json($status);
    }

    public function putNoteDone(){
        $noteId = $_REQUEST['note_id'];
        // 增加结束标签
        $tagId = Tags::$defaultTags['done'];
        $userId = $_SESSION['user_id'];
        // 之前此处有bug,结果浪费了很多时间
        $status = tags::toggleTodoAndDone($noteId, $userId, $tagId);
        Ajax::json($status);
    }

    public function putNoteTodo(){
        $noteId = $_REQUEST['note_id'];
        // 增加结束标签
        $tagId = Tags::$defaultTags['todo'];
        $userId = $_SESSION['user_id'];
        // 之前此处有bug,结果浪费了很多时间
        $status = tags::toggleTodoAndDone($noteId, $userId, $tagId);
        Ajax::json($status);
    }
}