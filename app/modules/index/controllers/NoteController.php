<?php
namespace n\modules\index\controllers;

use n\models\Image;
use n\models\Notes;
use n\models\Tags;
use nxn\web\Ajax;
use n\modules\account\controllers\AuthController;

use PDO;

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
        $result = Notes::notesByItem($userId, $itemId, $_REQUEST, [] );
        if ($_REQUEST) {
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
            Ajax::json(false, [], 'can not find related note wiht id:' . $_REQUEST['id']);
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
        $pictures = $_REQUEST['pictures'];
        if(!empty($pictures)){
            $imagesInDb =Image::findNoteImages($_REQUEST['id']);
            $imagesInDb = array_column($imagesInDb,null, 'index');
            foreach ($pictures as $index => $base64){
                $image = new Image();
                if(isset($imagesInDb[$index])){
                    $image->id = $imagesInDb[$index]->id;
                }
                $image->note_id = $_REQUEST['id'];
                $image->base64 = $base64;
                $image->index = $index;
                $image->save();
            }
        };
        $note->setAttributes($_REQUEST);
        $status = $note->save(false);
        $id = $note->id;
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
        l(['type_of_item_id' => $note->item_id]);;
        $status = $note->update(false);
        l(['status' => $status]);
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
}